<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionBulkResultsModel extends Model
{
    public function mapRows(array $rows): array
    {
        foreach ($rows as $index => &$row) {
            $parts = preg_split('/\s+/', trim($row['name']), 2);
            if ($parts === false || count($parts) !== 2) {
                $row['playerOptions'] = [];
            } else {
                $row['playerOptions'] = $this->db->table('competitor c')
                    ->select('c.competitor_id, c.first_name, c.last_name, c.iwf_id, r.rar_name')
                    ->join('rar r', 'r.rar_id = c.rar_id')
                    ->where('c.first_name', $parts[0])
                    ->where('c.last_name', $parts[1])
                    ->orderBy('c.competitor_id')
                    ->get()
                    ->getResultArray();
            }
            $row['armyOptions'] = $this->db->table('armies a')
                ->select('a.army_id, a.name, s.system_name, a.version')
                ->join('system s', 's.system_id = a.system_id')
                ->join('army_books ab', 'ab.book_id = a.book_id')
                ->where('ab.status', 'A')
                ->where('a.name', trim($row['army']))
                ->orderBy('a.army_id', 'DESC')
                ->get()
                ->getResultArray();
            $row['index'] = $index;
        }
        unset($row);

        return $rows;
    }

    public function getSelectionOptions(): array
    {
        return [
            'competitors' => $this->db->table('competitor c')
                ->select('c.competitor_id, c.first_name, c.last_name, c.iwf_id, r.rar_name')
                ->join('rar r', 'r.rar_id = c.rar_id')
                ->orderBy('c.last_name')
                ->orderBy('c.first_name')
                ->get()
                ->getResultArray(),
            'armies' => $this->db->table('armies a')
                ->select('a.army_id, a.name, s.system_name, a.version')
                ->join('system s', 's.system_id = a.system_id')
                ->join('army_books ab', 'ab.book_id = a.book_id')
                ->where('ab.status', 'A')
                ->orderBy('a.name')
                ->orderBy('s.system_name')
                ->orderBy('a.version')
                ->get()
                ->getResultArray(),
        ];
    }

    public function replaceResults(int $competitionId, string $competitionDate, array $rows, array $matches): bool
    {
        $this->db->transBegin();

        $existingMatchIds = array_column(
            $this->db->table('matches')
                ->select('match_id')
                ->where('competition_id', $competitionId)
                ->get()
                ->getResultArray(),
            'match_id'
        );
        if ($existingMatchIds !== []
            && ! $this->db->table('match_participant')->whereIn('match_id', $existingMatchIds)->delete()) {
            $this->db->transRollback();
            return false;
        }
        if (! $this->db->table('matches')->where('competition_id', $competitionId)->delete()
            || ! $this->db->table('position')->where('competition_id', $competitionId)->delete()) {
            $this->db->transRollback();
            return false;
        }

        foreach ($rows as $row) {
            if (! $this->db->table('position')->insert([
                'competition_id' => $competitionId,
                'competitor_id' => $row['competitorId'],
                'position' => $row['position'],
                'score' => $row['score'],
                'army_id' => $row['armyId'],
                'type' => 'P',
                'bonus' => 0,
                'team' => '',
                'pairing' => 0,
            ])) {
                $this->db->transRollback();
                return false;
            }
        }

        foreach ($matches as $match) {
            if (! $this->db->table('matches')->insert([
                'competition_id' => $competitionId,
                'round' => $match['round'],
                'winner_score' => $match['winnerScore'],
                'loser_score' => $match['loserScore'],
                'glicko_eligible' => 1,
                'glicko_processed' => 0,
                'match_date' => $competitionDate,
                'match_seqno' => str_replace('-', '', $competitionDate) . $match['round'],
                'pred_winner_score' => 0,
                'pred_loser_score' => 0,
            ])) {
                $this->db->transRollback();
                return false;
            }

            $matchId = (int) $this->db->insertID();
            $participants = [
                ['match_id' => $matchId, 'competitor_id' => $match['winnerId'], 'side' => 'W'],
            ];
            if ($match['loserId'] !== null) {
                $participants[] = ['match_id' => $matchId, 'competitor_id' => $match['loserId'], 'side' => 'L'];
            }
            foreach ($participants as $participant) {
                if (! $this->db->table('match_participant')->insert($participant)) {
                    $this->db->transRollback();
                    return false;
                }
            }
        }

        if (! $this->db->transStatus()) {
            $this->db->transRollback();
            return false;
        }

        return $this->db->transCommit();
    }
}
