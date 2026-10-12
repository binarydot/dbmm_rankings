<?php

namespace App\Models;

use CodeIgniter\Model;

class PlayerMatchesModel extends Model
{
    protected $table = 'matches';
    protected $primaryKey = 'match_id';

    public function getHistory(int $playerId): array
    {
        $history = $this->db->query(
            "SELECT c.competition_name,
                    c.competition_date,
                    c.competition_id,
                    m.match_id,
                    m.round,
                    m.winner_score,
                    m.loser_score,
                    mp.side,
                    a.name AS army,
                    a.army_id
             FROM competition c
             JOIN matches m ON m.competition_id = c.competition_id
             JOIN match_participant mp ON mp.match_id = m.match_id
             JOIN position p ON p.competitor_id = mp.competitor_id
                           AND p.competition_id = c.competition_id
             LEFT JOIN armies a ON a.army_id = p.army_id
             WHERE mp.competitor_id = ?
               AND p.type = 'P'
             UNION
             SELECT c.competition_name,
                    c.competition_date,
                    c.competition_id,
                    m.match_id,
                    m.round,
                    m.winner_score,
                    m.loser_score,
                    mp.side,
                    a.name AS army,
                    a.army_id
             FROM competition c
             JOIN matches m ON m.competition_id = c.competition_id
             JOIN match_participant mp ON mp.match_id = m.match_id
             JOIN stand_in s ON s.competitor_id = mp.competitor_id
                            AND s.competition_id = c.competition_id
             JOIN position p ON p.competition_id = c.competition_id
                            AND p.competitor_id = s.stood_in_for
             LEFT JOIN armies a ON a.army_id = p.army_id
             WHERE mp.competitor_id = ?
             ORDER BY competition_date, round",
            [$playerId, $playerId]
        )->getResultArray();

        if ($history === []) {
            return [];
        }

        $matchIds = array_values(array_unique(array_column($history, 'match_id')));
        $participants = $this->db->table('match_participant mp')
            ->select('mp.match_id, mp.side, mp.competitor_id, c.first_name, c.last_name, mp.army_id, a.name AS army_name')
            ->join('competitor c', 'c.competitor_id = mp.competitor_id')
            ->join('armies a', 'a.army_id = mp.army_id', 'left')
            ->whereIn('mp.match_id', $matchIds)
            ->orderBy('mp.match_id')
            ->orderBy('mp.side')
            ->orderBy('c.last_name')
            ->orderBy('c.first_name')
            ->get()
            ->getResultArray();

        $byMatch = [];
        foreach ($participants as $participant) {
            $side = $participant['side'] === 'W' ? 'winners' : 'losers';
            $byMatch[$participant['match_id']][$side][] = $participant;
        }

        foreach ($history as &$match) {
            $match['winners'] = $byMatch[$match['match_id']]['winners'] ?? [];
            $match['losers'] = $byMatch[$match['match_id']]['losers'] ?? [];
        }
        unset($match);

        return $history;
    }
}
