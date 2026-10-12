<?php

namespace App\Models;

use CodeIgniter\Model;

class MatchDetailsModel extends Model
{
    protected $table = 'matches';
    protected $primaryKey = 'match_id';

    public function getByYear(int $year): array
    {
        return $this->db->table('competition c')
            ->select('s.system_name, s.version, c.competition_date, m.round, c.competition_id, c.competition_name, p.competitor_id, player.first_name, player.last_name, p.army_id, army.name AS army_name, army.version AS army_version, army_system.system_name AS army_system, mp.side, m.winner_score, m.loser_score')
            ->join('position p', "p.competition_id = c.competition_id AND p.type = 'P'")
            ->join('competitor player', 'player.competitor_id = p.competitor_id')
            ->join('armies army', 'army.army_id = p.army_id')
            ->join('matches m', 'm.competition_id = c.competition_id')
            ->join('match_participant mp', 'mp.match_id = m.match_id AND mp.competitor_id = p.competitor_id')
            ->join('system s', 's.system_id = c.system_id')
            ->join('system army_system', 'army_system.system_id = army.system_id')
            ->where('YEAR(c.competition_date)', $year, false)
            ->orderBy('c.competition_date')
            ->orderBy('m.round')
            ->orderBy('m.match_id')
            ->orderBy('mp.side')
            ->orderBy('player.last_name')
            ->orderBy('player.first_name')
            ->get()
            ->getResultArray();
    }
}
