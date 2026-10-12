<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionResultsModel extends Model
{
    protected $table = 'position';
    protected $primaryKey = 'position_id';

    public function getCompetition(int $competitionId): ?array
    {
        return $this->db->table('competition c')
            ->select('c.competition_id, c.competition_name, c.competition_date, c.weighting, c.type, c.competition_series_id, cs.competition_series_name')
            ->join('competition_series cs', 'cs.competition_series_id = c.competition_series_id')
            ->where('c.competition_id', $competitionId)
            ->get()
            ->getRowArray();
    }

    public function hasResults(int $competitionId): bool
    {
        return $this->db->table('position')
            ->where('competition_id', $competitionId)
            ->where('position >', 0)
            ->countAllResults() > 0;
    }

    public function getResults(int $competitionId, bool $fullResults, float $weighting): array
    {
        $orderBy = $fullResults ? 'p.position ASC' : 'rating DESC';
        $rows = $this->db->query(
            "SELECT p.competitor_id,
                    CONCAT(c.first_name, ' ', c.last_name) AS competitor_name,
                    c.rar_id,
                    r.rar_name,
                    r.rar_tag,
                    p.position,
                    p.score,
                    p.position_id,
                    p.army_id,
                    p.bonus,
                    p.pairing,
                    a.name AS army_name,
                    COALESCE(g.rating, 2000) AS rating,
                    COALESCE((
                        SELECT SUM(mw.winner_score)
                        FROM matches mw
                        JOIN match_participant mpw ON mpw.match_id = mw.match_id
                        WHERE mw.competition_id = p.competition_id
                          AND mpw.competitor_id = p.competitor_id
                          AND mpw.side = 'W'
                    ), 0) AS winner_score_total,
                    COALESCE((
                        SELECT SUM(ml.loser_score)
                        FROM matches ml
                        JOIN match_participant mpl ON mpl.match_id = ml.match_id
                        WHERE ml.competition_id = p.competition_id
                          AND mpl.competitor_id = p.competitor_id
                          AND mpl.side = 'L'
                    ), 0) AS loser_score_total
             FROM position p
             JOIN competitor c ON c.competitor_id = p.competitor_id
             JOIN rar r ON r.rar_id = c.rar_id
             LEFT JOIN armies a ON a.army_id = p.army_id
             JOIN competition comp ON comp.competition_id = p.competition_id
             LEFT JOIN glicko_rating g
               ON g.competitor_id = p.competitor_id
              AND g.rating_date = (
                  SELECT MAX(g2.rating_date)
                  FROM glicko_rating g2
                  WHERE g2.competitor_id = g.competitor_id
                    AND g2.match_date < comp.competition_date
              )
             WHERE p.competition_id = ?
               AND p.type = 'P'
             ORDER BY {$orderBy}",
            [$competitionId]
        )->getResultArray();

        $maxPosition = (int) ($this->db->table('position')
            ->selectMax('position', 'max_position')
            ->where('competition_id', $competitionId)
            ->get()
            ->getRowArray()['max_position'] ?? 0);

        $totalRating = 0.0;
        foreach ($rows as &$row) {
            $row['ranking_points'] = null;
            $row['error_message'] = '';
            if ($fullResults) {
                $row['ranking_points'] = $maxPosition > 1
                    ? (($maxPosition - (int) $row['position']) / ($maxPosition - 1) * ($weighting - 1)) + 1
                    : $weighting;
                $matchScore = (float) $row['winner_score_total'] + (float) $row['loser_score_total'];
                $resultScore = (float) $row['score'] - (float) $row['bonus'];
                if (abs($matchScore - $resultScore) > 0.00001) {
                    $row['error_message'] = 'Match scores do not match result score!';
                }
            }
            $totalRating += (float) $row['rating'];
        }
        unset($row);

        return ['rows' => $rows, 'totalRating' => $totalRating];
    }

    public function getUmpires(int $competitionId): array
    {
        return $this->db->table('umpire u')
            ->select('u.umpire_id, c.competitor_id, c.first_name, c.last_name')
            ->join('competitor c', 'c.competitor_id = u.competitor_id')
            ->where('u.competition_id', $competitionId)
            ->orderBy('c.last_name')
            ->orderBy('c.first_name')
            ->get()
            ->getResultArray();
    }

    public function getStandIns(int $competitionId): array
    {
        return $this->db->table('stand_in s')
            ->select('s.stand_in_id, s.competition_id, standin.competitor_id, standin.first_name, standin.last_name, replaced.competitor_id AS stood_in_for_id, replaced.first_name AS stood_in_for_first_name, replaced.last_name AS stood_in_for_last_name')
            ->join('competitor standin', 'standin.competitor_id = s.competitor_id')
            ->join('competitor replaced', 'replaced.competitor_id = s.stood_in_for')
            ->where('s.competition_id', $competitionId)
            ->orderBy('standin.last_name')
            ->orderBy('standin.first_name')
            ->get()
            ->getResultArray();
    }

    public function getRoundStandings(int $competitionId): array
    {
        $rows = $this->db->table('matches m')
            ->select('m.round, mp.competitor_id, c.first_name, c.last_name, CASE WHEN mp.side = \'W\' THEN m.winner_score ELSE m.loser_score END AS score', false)
            ->join('match_participant mp', 'mp.match_id = m.match_id')
            ->join('competitor c', 'c.competitor_id = mp.competitor_id')
            ->where('m.competition_id', $competitionId)
            ->orderBy('m.round')
            ->get()
            ->getResultArray();

        $roundPoints = [];
        $names = [];
        $rounds = [];
        foreach ($rows as $row) {
            $playerId = (int) $row['competitor_id'];
            $round = (int) $row['round'];
            $names[$playerId] = trim($row['first_name'] . ' ' . $row['last_name']);
            $roundPoints[$round][$playerId] = ($roundPoints[$round][$playerId] ?? 0) + (float) $row['score'];
            $rounds[$round] = true;
        }

        ksort($rounds);
        $roundNumbers = array_keys($rounds);
        $standings = [];
        $cumulativeScores = array_fill_keys(array_keys($names), 0.0);
        foreach ($roundNumbers as $round) {
            foreach ($names as $playerId => $name) {
                $cumulativeScores[$playerId] += $roundPoints[$round][$playerId] ?? 0;
                $standings[$playerId]['name'] = $name;
                $standings[$playerId]['scores'][$round] = $roundPoints[$round][$playerId] ?? 0;
                $standings[$playerId]['cumulative'][$round] = $cumulativeScores[$playerId];
            }

            $sortedScores = $cumulativeScores;
            arsort($sortedScores, SORT_NUMERIC);
            $position = 0;
            $previousScore = null;
            $previousPosition = 0;
            foreach ($sortedScores as $playerId => $score) {
                $position++;
                $currentPosition = $previousScore !== null && $score === $previousScore
                    ? $previousPosition
                    : $position;
                $standings[$playerId]['positions'][$round] = $currentPosition;
                $previousScore = $score;
                $previousPosition = $currentPosition;
            }
        }

        return ['rounds' => $roundNumbers, 'players' => array_values($standings)];
    }
}
