<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnualTrendsModel extends Model
{
    protected $table = 'matches';
    protected $primaryKey = 'match_id';

    public function getTrends(): array
    {
        $trends = [];
        $matchCounts = $this->db->query(
            'SELECT YEAR(match_date) AS year, COUNT(*) AS matches
             FROM matches
             GROUP BY YEAR(match_date)
             ORDER BY YEAR(match_date)'
        )->getResultArray();

        foreach ($matchCounts as $row) {
            $year = (int) $row['year'];
            $trends[$year] = [
                'matches' => (int) $row['matches'],
                'players' => 0,
                'newbies' => 0,
            ];
        }

        $playerCounts = $this->db->query(
            'SELECT YEAR(c.competition_date) AS year,
                    COUNT(DISTINCT p.competitor_id) AS players
             FROM competition c
             JOIN position p ON p.competition_id = c.competition_id
             GROUP BY YEAR(c.competition_date)
             ORDER BY YEAR(c.competition_date)'
        )->getResultArray();

        foreach ($playerCounts as $row) {
            $year = (int) $row['year'];
            $trends[$year] ??= ['matches' => 0, 'players' => 0, 'newbies' => 0];
            $trends[$year]['players'] = (int) $row['players'];
        }

        $newPlayerCounts = $this->db->query(
            'SELECT YEAR(c.competition_date) AS year,
                    COUNT(DISTINCT p.competitor_id) AS newbies
             FROM competition c
             JOIN position p ON p.competition_id = c.competition_id
             WHERE YEAR(c.competition_date) = (
                 SELECT MIN(YEAR(first_competition.competition_date))
                 FROM competition first_competition
                 JOIN position first_position
                   ON first_position.competition_id = first_competition.competition_id
                 WHERE first_position.competitor_id = p.competitor_id
             )
             GROUP BY YEAR(c.competition_date)
             ORDER BY YEAR(c.competition_date)'
        )->getResultArray();

        foreach ($newPlayerCounts as $row) {
            $year = (int) $row['year'];
            $trends[$year] ??= ['matches' => 0, 'players' => 0, 'newbies' => 0];
            $trends[$year]['newbies'] = (int) $row['newbies'];
        }

        ksort($trends);

        return $trends;
    }
}
