<?php

namespace App\Models;

use CodeIgniter\Model;

class PlayerGlickoModel extends Model
{
    protected $table = 'glicko_rating';
    protected $primaryKey = 'match_id';

    public function getHistory(int $playerId): array
    {
        $history = $this->db->table('glicko_rating gr')
            ->select('gr.rating, gr.reliability, gr.match_id, gr.rating_date, c.competition_date, c.competition_name, c.competition_id, m.round, m.winner_score, m.loser_score, mp.side')
            ->join('matches m', 'm.match_id = gr.match_id')
            ->join('competition c', 'c.competition_id = m.competition_id')
            ->join('match_participant mp', 'mp.match_id = m.match_id AND mp.competitor_id = gr.competitor_id')
            ->where('gr.competitor_id', $playerId)
            ->where('gr.status', 'A')
            ->orderBy('gr.rating_date', 'ASC')
            ->orderBy('m.round', 'ASC')
            ->get()
            ->getResultArray();

        if ($history === []) {
            return [];
        }

        $matchIds = array_values(array_unique(array_column($history, 'match_id')));
        $opponents = $this->db->table('match_participant own')
            ->select('own.match_id, opponent.competitor_id, opponent.first_name, opponent.last_name, rating.rating, rating.reliability, previous.rating AS previous_rating, previous.reliability AS previous_reliability')
            ->join('match_participant other', 'other.match_id = own.match_id AND other.side != own.side', 'inner')
            ->join('competitor opponent', 'opponent.competitor_id = other.competitor_id')
            ->join('glicko_rating rating', "rating.competitor_id = other.competitor_id AND rating.match_id = own.match_id AND rating.status = 'A'", 'inner', false)
            ->join(
                'glicko_rating previous',
                'previous.competitor_id = rating.competitor_id AND previous.rating_date = (
                    SELECT MAX(prior.rating_date)
                    FROM glicko_rating prior
                    WHERE prior.competitor_id = rating.competitor_id
                      AND prior.rating_date < rating.rating_date
                )',
                'left',
                false
            )
            ->where('own.competitor_id', $playerId)
            ->whereIn('own.match_id', $matchIds)
            ->orderBy('own.match_id')
            ->orderBy('opponent.last_name')
            ->orderBy('opponent.first_name')
            ->get()
            ->getResultArray();

        $opponentsByMatch = [];
        foreach ($opponents as $opponent) {
            $opponentsByMatch[$opponent['match_id']][] = [
                'id' => (int) $opponent['competitor_id'],
                'name' => trim($opponent['first_name'] . ' ' . $opponent['last_name']),
                'rating' => $opponent['rating'],
                'reliability' => $opponent['reliability'],
                'previousRating' => $opponent['previous_rating'] ?? 'N/A',
                'previousReliability' => $opponent['previous_reliability'] ?? 'N/A',
            ];
        }

        foreach ($history as &$record) {
            $record['myScore'] = $record['side'] === 'W' ? $record['winner_score'] : $record['loser_score'];
            $record['yourScore'] = $record['side'] === 'W' ? $record['loser_score'] : $record['winner_score'];
            $record['opponents'] = $opponentsByMatch[$record['match_id']] ?? [[
                'id' => null,
                'name' => 'Cannot be found',
                'rating' => 'N/A',
                'reliability' => 'N/A',
                'previousRating' => 'N/A',
                'previousReliability' => 'N/A',
            ]];
        }
        unset($record);

        return $history;
    }
}
