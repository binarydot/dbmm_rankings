<?php

namespace App\Models;

use CodeIgniter\Model;

class ArmyDirectoryModel extends Model
{
    protected $table = 'armies';
    protected $primaryKey = 'army_id';

    public function getActiveArmies(): array
    {
        return $this->db->table('armies a')
            ->select('a.army_id, a.name, a.book_number, a.list_number, a.start_year, a.end_year, s.system_name, a.version')
            ->join('system s', 's.system_id = a.system_id')
            ->join('army_books ab', 'ab.book_id = a.book_id')
            ->where('ab.status', 'A')
            ->orderBy('a.name')
            ->orderBy('s.system_name')
            ->orderBy('a.version')
            ->get()
            ->getResultArray();
    }

    public function getArmy(int $armyId): ?array
    {
        return $this->db->table('armies a')
            ->select('a.army_id, a.name, a.book_number, a.list_number, a.start_year, a.end_year, a.version, s.system_name')
            ->join('system s', 's.system_id = a.system_id')
            ->where('a.army_id', $armyId)
            ->get()
            ->getRowArray();
    }
}
