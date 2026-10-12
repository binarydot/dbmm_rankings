<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionMatchesModel extends Model
{
    protected $table = 'matches';
    protected $primaryKey = 'match_id';

    public function getCompetition(int $competitionId): ?array
    {
        return $this->db->table('competition c')
            ->select('c.competition_id, c.competition_name, c.competition_date, c.type, c.competition_series_id, cs.competition_series_name')
            ->join('competition_series cs', 'cs.competition_series_id = c.competition_series_id')
            ->where('c.competition_id', $competitionId)
            ->get()
            ->getRowArray();
    }

    public function getRounds(int $competitionId): array
    {
        $matches = $this->db->table('matches')
            ->select('match_id, round, winner_score, loser_score')
            ->where('competition_id', $competitionId)
            ->orderBy('round')
            ->orderBy('winner_score', 'DESC')
            ->get()
            ->getResultArray();

        if ($matches === []) {
            return [];
        }

        $participants = $this->db->table('match_participant mp')
            ->select('mp.match_id, mp.side, c.competitor_id, c.first_name, c.last_name, COALESCE(mp.army_id, p.army_id) AS army_id, a.name AS army_name')
            ->join('competitor c', 'c.competitor_id = mp.competitor_id')
            ->join('position p', 'p.competitor_id = mp.competitor_id AND p.competition_id = ' . (int) $competitionId, 'left', false)
            ->join('armies a', 'a.army_id = COALESCE(mp.army_id, p.army_id)', 'left', false)
            ->join('matches m', 'm.match_id = mp.match_id')
            ->where('m.competition_id', $competitionId)
            ->orderBy('mp.match_id')
            ->orderBy('mp.side')
            ->orderBy('c.last_name')
            ->orderBy('c.first_name')
            ->get()
            ->getResultArray();

        $byMatch = [];
        foreach ($participants as $participant) {
            $side = $participant['side'] === 'W' ? 'winners' : 'losers';
            $byMatch[$participant['match_id']][$side][] = [
                'id' => (int) $participant['competitor_id'],
                'name' => trim($participant['first_name'] . ' ' . $participant['last_name']),
                'armyId' => $participant['army_id'] === null ? null : (int) $participant['army_id'],
                'armyName' => $participant['army_name'],
            ];
        }

        $rounds = [];
        foreach ($matches as $match) {
            $matchId = (int) $match['match_id'];
            $round = (string) $match['round'];
            $rounds[$round][] = [
                'id' => $matchId,
                'winnerScore' => $match['winner_score'],
                'loserScore' => $match['loser_score'],
                'score' => $match['winner_score'] . ' - ' . $match['loser_score'],
                'error' => (float) $match['winner_score'] + (float) $match['loser_score'] !== 25.0,
                'winners' => $byMatch[$matchId]['winners'] ?? [],
                'losers' => $byMatch[$matchId]['losers'] ?? [],
            ];
        }

        return $rounds;
    }
}
