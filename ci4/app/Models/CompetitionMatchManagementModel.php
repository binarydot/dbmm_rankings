<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionMatchManagementModel extends Model
{
    protected $table = 'matches';
    protected $primaryKey = 'match_id';

    public function getCompetitors(int $competitionId): array
    {
        return $this->db->query(
            "SELECT c.competitor_id, c.first_name, c.last_name, MAX(p.army_id) AS army_id
             FROM competitor c
             JOIN position p ON p.competitor_id = c.competitor_id
             WHERE p.competition_id = ? AND p.type = 'P'
             GROUP BY c.competitor_id, c.first_name, c.last_name
             UNION
             SELECT c.competitor_id, c.first_name, c.last_name, MAX(p.army_id) AS army_id
             FROM stand_in s
             JOIN competitor c ON c.competitor_id = s.competitor_id
             LEFT JOIN position p
               ON p.competitor_id = s.stood_in_for
              AND p.competition_id = s.competition_id
              AND p.type = 'P'
             WHERE s.competition_id = ?
             GROUP BY c.competitor_id, c.first_name, c.last_name
             ORDER BY first_name, last_name",
            [$competitionId, $competitionId]
        )->getResultArray();
    }

    public function getArmies(): array
    {
        return $this->db->table('armies a')
            ->select('a.army_id, a.name, s.system_name, a.version')
            ->join('system s', 's.system_id = a.system_id')
            ->join('army_books ab', 'ab.book_id = a.book_id')
            ->where('ab.status', 'A')
            ->orderBy('a.name')
            ->orderBy('s.system_name')
            ->orderBy('a.version')
            ->get()
            ->getResultArray();
    }

    public function getMatch(int $competitionId, int $matchId): ?array
    {
        $match = $this->db->table('matches')
            ->where('competition_id', $competitionId)
            ->where('match_id', $matchId)
            ->get()
            ->getRowArray();
        if ($match === null) {
            return null;
        }

        $participants = $this->db->table('match_participant')
            ->where('match_id', $matchId)
            ->orderBy('side')
            ->orderBy('competitor_id')
            ->get()
            ->getResultArray();
        $match['winners'] = [];
        $match['losers'] = [];
        foreach ($participants as $participant) {
            $side = $participant['side'] === 'W' ? 'winners' : 'losers';
            $match[$side][] = $participant;
        }

        return $match;
    }

    public function saveMatch(int $competitionId, ?int $matchId, array $match, array $participants): int|false
    {
        $this->db->transBegin();
        if ($matchId === null) {
            $match['competition_id'] = $competitionId;
            $match['glicko_processed'] = 'N';
            $match['pred_winner_score'] = 0;
            $match['pred_loser_score'] = 0;
            if (! $this->db->table($this->table)->insert($match)) {
                $this->db->transRollback();
                return false;
            }

            $matchId = (int) $this->db->insertID();
        } else {
            $match['glicko_processed'] = 'N';
            $match['pred_winner_score'] = 0;
            $match['pred_loser_score'] = 0;
            $updated = $this->db->table($this->table)
                ->where('competition_id', $competitionId)
                ->where('match_id', $matchId)
                ->update($match);
            if (! $updated) {
                $this->db->transRollback();
                return false;
            }

            $this->db->table('match_participant')->where('match_id', $matchId)->delete();
        }

        foreach ($participants as $participant) {
            $participant['match_id'] = $matchId;
            if (! $this->db->table('match_participant')->insert($participant)) {
                $this->db->transRollback();
                return false;
            }
        }

        if (! $this->db->transStatus()) {
            $this->db->transRollback();
            return false;
        }

        return $this->db->transCommit() ? $matchId : false;
    }

    public function deleteMatch(int $competitionId, int $matchId): bool
    {
        $this->db->transBegin();
        $deleted = $this->db->table($this->table)
            ->where('competition_id', $competitionId)
            ->where('match_id', $matchId)
            ->delete();

        if (! $deleted || $this->db->affectedRows() !== 1 || ! $this->db->transStatus()) {
            $this->db->transRollback();
            return false;
        }

        $this->db->table('match_participant')->where('match_id', $matchId)->delete();
        if (! $this->db->transStatus()) {
            $this->db->transRollback();
            return false;
        }

        return $this->db->transCommit();
    }
}
