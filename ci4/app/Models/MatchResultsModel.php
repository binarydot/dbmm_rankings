<?php

namespace App\Models;

use CodeIgniter\Model;

class MatchResultsModel extends Model
{
    protected $table = 'matches';
    protected $primaryKey = 'match_id';

    public function getScoreLabels(): array
    {
        $labels = [];
        for ($winnerScore = 25; $winnerScore >= 13; $winnerScore--) {
            $labels[] = $winnerScore . ' - ' . (25 - $winnerScore);
        }

        return $labels;
    }

    public function getResults(): array
    {
        $rows = $this->db->query(
            "SELECT s.system_id, s.system_name, s.version, m.winner_score,
                    m.loser_score, COUNT(*) AS total_matches
             FROM matches m
             JOIN competition c ON c.competition_id = m.competition_id
             JOIN system s ON s.system_id = c.system_id
             WHERE m.winner_score + m.loser_score = 25
               AND m.winner_score < 26
               AND m.winner_score > 12
             GROUP BY s.system_id, s.system_name, s.version,
                      m.winner_score, m.loser_score
             ORDER BY s.system_name, s.version, m.winner_score DESC"
        )->getResultArray();

        $systems = [];
        foreach ($rows as $row) {
            $systemId = (int) $row['system_id'];
            if (! isset($systems[$systemId])) {
                $systems[$systemId] = [
                    'name' => trim($row['system_name'] . ' ' . $row['version']),
                    'scores' => [],
                    'total' => 0,
                ];
            }

            $score = (int) $row['winner_score'] . ' - ' . (int) $row['loser_score'];
            $count = (int) $row['total_matches'];
            $systems[$systemId]['scores'][$score] = [
                'number' => $count,
                'percent' => 0.0,
            ];
            $systems[$systemId]['total'] += $count;
        }

        foreach ($systems as &$system) {
            foreach ($system['scores'] as &$score) {
                $score['percent'] = $system['total'] === 0
                    ? 0.0
                    : round($score['number'] / $system['total'] * 100, 2);
            }
            unset($score);
        }
        unset($system);

        return $systems;
    }
}
