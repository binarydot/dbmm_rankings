<?php

namespace App\Models;

use CodeIgniter\Model;

class PlayerResultsModel extends Model
{
    protected $table = 'position';
    protected $primaryKey = 'position_id';

    public function getHistory(int $playerId): array
    {
        return $this->db->table('position p')
            ->select('c.competition_id, c.competition_name, c.competition_date, p.position, p.score, p.bonus, a.name AS army')
            ->join('competition c', 'c.competition_id = p.competition_id')
            ->join('armies a', 'a.army_id = p.army_id', 'left')
            ->where('p.competitor_id', $playerId)
            ->where('p.position >', 0)
            ->orderBy('c.competition_date', 'ASC')
            ->get()
            ->getResultArray();
    }
}
