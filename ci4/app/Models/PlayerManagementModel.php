<?php

namespace App\Models;

use CodeIgniter\Model;

class PlayerManagementModel extends Model
{
    protected $table = 'competitor';
    protected $primaryKey = 'competitor_id';

    public function regionExists(int $regionId): bool
    {
        return $this->db->table('rar')->where('rar_id', $regionId)->countAllResults() === 1;
    }

    public function insertPlayer(array $player): int|false
    {
        if (! $this->db->table($this->table)->insert($player)) {
            return false;
        }

        return (int) $this->db->insertID();
    }

    public function updatePlayer(int $playerId, array $player): bool
    {
        $exists = $this->db->table($this->table)
            ->where('competitor_id', $playerId)
            ->countAllResults() === 1;
        if (! $exists) {
            return false;
        }

        return (bool) $this->db->table($this->table)
            ->where('competitor_id', $playerId)
            ->update($player);
    }

    public function canDeletePlayer(int $playerId): bool
    {
        $checks = [
            ['position', 'competitor_id'],
            ['match_participant', 'competitor_id'],
            ['umpire', 'competitor_id'],
            ['stand_in', 'competitor_id'],
            ['stand_in', 'stood_in_for'],
            ['glicko_rating', 'competitor_id'],
            ['glicko_rating_bak', 'competitor_id'],
            ['ranking', 'competitor_id'],
        ];
        foreach ($checks as [$table, $column]) {
            if ($this->db->table($table)->where($column, $playerId)->countAllResults() > 0) {
                return false;
            }
        }

        return true;
    }

    public function deletePlayer(int $playerId): bool
    {
        if (! $this->canDeletePlayer($playerId)) {
            return false;
        }

        return (bool) $this->db->table($this->table)
            ->where('competitor_id', $playerId)
            ->delete()
            && $this->db->affectedRows() === 1;
    }
}
