<?php

namespace App\Models;

use CodeIgniter\Model;

class ArmyMatchesModel extends Model
{
    protected $table = 'armies';
    protected $primaryKey = 'army_id';

    public function getArmy(int $armyId): ?array
    {
        return $this->db->table('armies')
            ->select('army_id, name')
            ->where('army_id', $armyId)
            ->get()
            ->getRowArray();
    }

    public function getArmyMatches(int $armyId): array
    {
        $rows = $this->db->query(
            "SELECT m.match_id, m.round, m.winner_score, m.loser_score,
                    c.competition_id, c.competition_date, c.competition_name,
                    mp.side, competitor.competitor_id, competitor.first_name,
                    competitor.last_name,
                    COALESCE(mp.army_id, p.army_id) AS army_id,
                    army.name AS army_name
             FROM matches m
             JOIN competition c ON c.competition_id = m.competition_id
             JOIN match_participant mp ON mp.match_id = m.match_id
             JOIN competitor ON competitor.competitor_id = mp.competitor_id
             LEFT JOIN position p
               ON p.competition_id = m.competition_id
              AND p.competitor_id = mp.competitor_id
              AND p.type = 'P'
             LEFT JOIN armies army
               ON army.army_id = COALESCE(mp.army_id, p.army_id)
             WHERE EXISTS (
                 SELECT 1
                 FROM match_participant focal_mp
                 JOIN position focal_p
                   ON focal_p.competition_id = m.competition_id
                  AND focal_p.competitor_id = focal_mp.competitor_id
                  AND focal_p.type = 'P'
                 WHERE focal_mp.match_id = m.match_id
                   AND focal_p.army_id = ?
             )
             ORDER BY c.competition_date, m.round, m.match_id, mp.side,
                      competitor.last_name, competitor.first_name",
            [$armyId]
        )->getResultArray();

        $matches = [];
        foreach ($rows as $row) {
            $matchId = (int) $row['match_id'];
            if (! isset($matches[$matchId])) {
                $matches[$matchId] = [
                    'competition_id' => (int) $row['competition_id'],
                    'competition_date' => $row['competition_date'],
                    'competition_name' => $row['competition_name'],
                    'round' => $row['round'],
                    'winner_score' => $row['winner_score'],
                    'loser_score' => $row['loser_score'],
                    'sides' => ['W' => [], 'L' => []],
                ];
            }

            if ($row['side'] !== 'W' && $row['side'] !== 'L') {
                continue;
            }

            $matches[$matchId]['sides'][$row['side']][] = [
                'id' => (int) $row['competitor_id'],
                'name' => trim($row['first_name'] . ' ' . $row['last_name']),
                'army_id' => $row['army_id'] === null ? null : (int) $row['army_id'],
                'army_name' => $row['army_name'],
            ];
        }

        $armyMatches = [];
        foreach ($matches as $match) {
            foreach (['W', 'L'] as $focalSide) {
                $opposingSide = $focalSide === 'W' ? 'L' : 'W';
                $players = $match['sides'][$focalSide];
                $opponents = $match['sides'][$opposingSide];
                $armyIsUsedOnSide = false;
                foreach ($players as $player) {
                    if ($player['army_id'] === $armyId) {
                        $armyIsUsedOnSide = true;
                        break;
                    }
                }

                if (! $armyIsUsedOnSide) {
                    continue;
                }

                $otherArmy = null;
                foreach ($opponents as $opponent) {
                    if ($opponent['army_id'] !== null) {
                        $otherArmy = [
                            'id' => $opponent['army_id'],
                            'name' => $opponent['army_name'],
                        ];
                        break;
                    }
                }

                $armyMatches[] = [
                    'competition_id' => $match['competition_id'],
                    'competition_date' => $match['competition_date'],
                    'competition_name' => $match['competition_name'],
                    'round' => $match['round'],
                    'players' => $players,
                    'opponents' => $opponents,
                    'score' => $focalSide === 'W'
                        ? $match['winner_score'] . ' - ' . $match['loser_score']
                        : $match['loser_score'] . ' - ' . $match['winner_score'],
                    'other_army' => $otherArmy,
                ];
            }
        }

        return $armyMatches;
    }
}
