<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserManagementModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public function getPermissions(): array
    {
        return $this->db->table('permissions')
            ->select('permission_id, name, description')
            ->orderBy('name')
            ->get()
            ->getResultArray();
    }

    public function getSeries(): array
    {
        return $this->db->table('competition_series')
            ->select('competition_series_id AS id, competition_series_name AS name')
            ->orderBy('competition_series_name')
            ->get()
            ->getResultArray();
    }

    public function usernameExists(string $username, ?int $excludeUserId = null): bool
    {
        $builder = $this->db->table('users')->where('user_name', $username);
        if ($excludeUserId !== null) {
            $builder->where('user_id !=', $excludeUserId);
        }

        return $builder->countAllResults() > 0;
    }

    public function saveUser(?int $userId, array $user, array $permissions, array $series): int|false
    {
        $this->db->transBegin();
        if ($userId === null) {
            if (! $this->db->table('users')->insert($user)) {
                $this->db->transRollback();
                return false;
            }
            $userId = (int) $this->db->insertID();
        } else {
            if (! $this->db->table('users')->where('user_id', $userId)->update($user)) {
                $this->db->transRollback();
                return false;
            }
        }

        $this->db->table('user_permissions')->where('user_id', $userId)->delete();
        foreach ($permissions as $permissionId => $value) {
            if (! $this->db->table('user_permissions')->insert([
                'user_id' => $userId,
                'permission_id' => $permissionId,
                'value' => $value,
            ])) {
                $this->db->transRollback();
                return false;
            }
        }

        $this->db->table('user_series')->where('user_id', $userId)->delete();
        foreach ($series as $seriesId => $value) {
            if (! $this->db->table('user_series')->insert([
                'user_id' => $userId,
                'competition_series_id' => $seriesId,
                'value' => $value,
            ])) {
                $this->db->transRollback();
                return false;
            }
        }

        if (! $this->db->transStatus()) {
            $this->db->transRollback();
            return false;
        }

        return $this->db->transCommit() ? $userId : false;
    }

    public function getPassword(int $userId): ?string
    {
        $row = $this->db->table('users')
            ->select('password_enc')
            ->where('user_id', $userId)
            ->where('activated', 1)
            ->get()
            ->getRowArray();

        return $row['password_enc'] ?? null;
    }

    public function setPassword(int $userId, string $passwordHash): bool
    {
        $exists = $this->db->table('users')
            ->where('user_id', $userId)
            ->countAllResults() === 1;
        if (! $exists) {
            return false;
        }

        return (bool) $this->db->table('users')
            ->where('user_id', $userId)
            ->update(['password_enc' => $passwordHash, 'enctype' => 'md5']);
    }
}
