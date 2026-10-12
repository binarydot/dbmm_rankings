<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTimeImmutable;

class CompetitionDirectoryModel extends Model
{
    protected $table = 'competition';
    protected $primaryKey = 'competition_id';

    public function getRegions(): array
    {
        return $this->db->table('rar')
            ->select('rar_id, rar_name')
            ->orderBy('rar_name')
            ->get()
            ->getResultArray();
    }

    public function getSeries(): array
    {
        return $this->db->table('competition_series')
            ->select('competition_series_id AS id, competition_series_name AS name')
            ->orderBy('competition_series_name')
            ->get()
            ->getResultArray();
    }

    public function getCompetitions(int $regionId, int $seriesId, int $year): array
    {
        $builder = $this->db->table('competition c')
            ->select('c.competition_id, c.competition_name, c.competition_date, c.type, c.weighting, c.protected, c.competition_series_id AS series_id, cs.competition_series_name, r.rar_name')
            ->select('(SELECT MAX(p.position) FROM position p WHERE p.competition_id = c.competition_id) AS max_position', false)
            ->select('(SELECT COUNT(p.position) FROM position p WHERE p.competition_id = c.competition_id) AS player_count', false)
            ->select('(SELECT COUNT(m.match_id) FROM matches m WHERE m.competition_id = c.competition_id) AS match_count', false)
            ->join('competition_series cs', 'cs.competition_series_id = c.competition_series_id')
            ->join('rar r', 'r.rar_id = c.rar_id')
            ->orderBy('c.competition_date', 'DESC');

        if ($regionId > 0) {
            $builder->where('c.rar_id', $regionId);
        }

        if ($seriesId > 0) {
            $builder->where('c.competition_series_id', $seriesId);
        }

        if ($year > 0) {
            $builder->where('YEAR(c.competition_date)', $year, false);
        } elseif ($year === -1) {
            $builder->where('YEAR(c.competition_date) >=', (int) date('Y'), false);
        }

        $rows = $builder->get()->getResultArray();
        foreach ($rows as &$row) {
            $row['competition_date'] = (new DateTimeImmutable($row['competition_date']))->format('jS F Y');
            $row['runners'] = (int) $row['max_position'] <= 0;
            $row['entrants'] = (int) $row['max_position'];
            $row['players'] = (int) $row['player_count'];
            $row['notes'] = $this->getCompetitionNotes($row);
        }
        unset($row);

        return $rows;
    }

    private function getCompetitionNotes(array $competition): string
    {
        $entrants = (int) $competition['player_count'];
        $maxPosition = (int) $competition['max_position'];
        $notes = '';

        if (($competition['type'] === 'Singles' && $maxPosition !== $entrants)
            || ($competition['type'] === 'Doubles' && $maxPosition !== $entrants / 2)) {
            $notes = 'Results missing. ';
        }

        if ($entrants === 0) {
            $notes = 'No results. ';
        }

        $matches = (int) $competition['match_count'];
        if ($matches > 0) {
            $matchesPerRound = intdiv($entrants, 2);
            if ($matchesPerRound === 0 || $matches % $matchesPerRound !== 0) {
                $notes .= 'Matches may be missing. ';
            }
        } else {
            $notes .= 'No matches. ';
        }

        return $notes;
    }
}
