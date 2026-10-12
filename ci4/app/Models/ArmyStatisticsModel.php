<?php

namespace App\Models;

use CodeIgniter\Model;

class ArmyStatisticsModel extends Model
{
    protected $table = 'armies';
    protected $primaryKey = 'army_id';

    public function getStatistics(string $viewType, int $monthsBack): array
    {
        $rows = $this->db->query(
            "SELECT p.army_id, a.book_number, a.list_number, a.name AS army_name,
                    a.system_id, s.system_name, a.version, m.match_id, m.round,
                    m.winner_score, m.loser_score, c.competition_date,
                    c.competition_id, c.competition_name, mp.side,
                    competitor.first_name, competitor.last_name,
                    DATEDIFF(CURDATE(), m.match_date) < 365 AS is_recent
             FROM matches m
             JOIN position p ON p.competition_id = m.competition_id
             JOIN match_participant mp
               ON mp.match_id = m.match_id
              AND mp.competitor_id = p.competitor_id
             JOIN armies a ON a.army_id = p.army_id
             JOIN system s ON s.system_id = a.system_id
             JOIN competition c ON c.competition_id = m.competition_id
             JOIN competitor ON competitor.competitor_id = p.competitor_id
             WHERE m.match_id IS NOT NULL
               AND p.type = 'P'
             ORDER BY c.competition_date, m.round, m.match_id",
            []
        )->getResultArray();

        $armies = [];
        foreach ($rows as $row) {
            $armyId = (int) $row['army_id'];
            if (! isset($armies[$armyId])) {
                $armies[$armyId] = [
                    'armyID' => $armyId,
                    'armyBook' => $row['book_number'],
                    'armyList' => $row['list_number'],
                    'armyName' => $row['army_name'],
                    'rulesSystemName' => $row['system_name'],
                    'armyListVersion' => $row['version'],
                    'systemId' => (int) $row['system_id'],
                    'numMatches' => 0,
                    'score_samples' => [],
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'first_use' => null,
                ];
            }

            if ($monthsBack === 0 || (int) $row['is_recent'] === 1) {
                $armies[$armyId]['numMatches']++;
            }
            $side = $row['side'];
            $matchId = (int) $row['match_id'];
            $sampleKey = $matchId . ':' . $side;
            if (($row['winner_score'] + $row['loser_score']) == 25) {
                $score = $side === 'W' ? (float) $row['winner_score'] : (float) $row['loser_score'];
                $armies[$armyId]['score_samples'][$sampleKey] = $score;
                if (! isset($armies[$armyId]['legacy_match_sides'][$sampleKey])) {
                    if ($side === 'W') {
                        $score >= 16 ? $armies[$armyId]['wins']++ : $armies[$armyId]['draws']++;
                    } elseif ($side === 'L') {
                        $score <= 10 ? $armies[$armyId]['losses']++ : $armies[$armyId]['draws']++;
                    }
                    $armies[$armyId]['legacy_match_sides'][$sampleKey] = true;
                }
            } elseif (($row['winner_score'] + $row['loser_score']) >= 32) {
                $score = $side === 'W'
                    ? $this->convert32To25(32 - (int) $row['loser_score'])
                    : $this->convert32To25((int) $row['loser_score']);
                $armies[$armyId]['score_samples'][$sampleKey] = (float) $score;
            }

            if ($viewType === 'firstused' && $armies[$armyId]['first_use'] === null) {
                $armies[$armyId]['first_use'] = [
                    'date' => $row['competition_date'],
                    'competition_id' => (int) $row['competition_id'],
                    'competition_name' => $row['competition_name'],
                    'round' => $row['round'],
                    'player_name' => trim($row['first_name'] . ' ' . $row['last_name']),
                ];
            }
        }

        $results = [];
        foreach ($armies as $army) {
            if ($army['numMatches'] === 0) {
                continue;
            }

            $sampleCount = count($army['score_samples']);
            if ($sampleCount > 0) {
                $army['avgScore'] = array_sum($army['score_samples']) / $sampleCount;
                $army['avgScoreHover'] = 'Based on ' . $sampleCount . ' match results.';
                $army['percentWins'] = round(($army['wins'] / $sampleCount) * 100);
                $army['percentDraws'] = round(($army['draws'] / $sampleCount) * 100);
                $army['percentLosses'] = round(($army['losses'] / $sampleCount) * 100);
                $army['topDog'] = $this->calculateTopDog($army['avgScore'], $sampleCount);
            } else {
                $army['avgScore'] = 'N/A';
                $army['avgScoreHover'] = '';
                $army['percentWins'] = 'N/A';
                $army['percentDraws'] = 'N/A';
                $army['percentLosses'] = 'N/A';
                $army['topDog'] = 0;
            }

            unset($army['score_samples'], $army['wins'], $army['draws'], $army['losses'], $army['legacy_match_sides']);
            $results[] = $army;
        }

        if ($viewType === 'mostused') {
            usort($results, static fn (array $left, array $right): int => $right['numMatches'] <=> $left['numMatches']);
            $rank = 0;
            $previousCount = null;
            foreach ($results as &$army) {
                if ($army['numMatches'] !== $previousCount) {
                    $rank++;
                    $previousCount = $army['numMatches'];
                }
                $army['armyRank'] = $rank;
            }
            unset($army);
        } else {
            usort($results, static function (array $left, array $right): int {
                return ($left['armyBook'] <=> $right['armyBook'])
                    ?: ($left['armyList'] <=> $right['armyList'])
                    ?: ($right['systemId'] <=> $left['systemId']);
            });
        }

        return $results;
    }

    private function calculateTopDog(float $average, int $sampleCount): float
    {
        if ($sampleCount < 8) {
            return $average * 0.25;
        }
        if ($sampleCount < 16) {
            return $average * 0.5;
        }
        if ($sampleCount < 24) {
            return $average * 0.75;
        }

        return $average + 2 * (floor($sampleCount / 100) - 1);
    }

    private function convert32To25(int $score): int
    {
        return match ($score) {
            32 => 25,
            31 => 24,
            30, 29 => 23,
            28 => 22,
            27 => 21,
            26 => 20,
            25, 24 => 19,
            23 => 18,
            22 => 17,
            21, 20 => 16,
            19 => 15,
            18 => 14,
            17, 16 => 13,
            15, 14, 13 => 12,
            default => $score,
        };
    }
}
