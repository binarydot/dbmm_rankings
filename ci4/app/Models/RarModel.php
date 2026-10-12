<?php

namespace App\Models;

use CodeIgniter\Model;

class RarModel extends Model
{
    public function getRars(): array
    {
        return $this->db->table('rar')
            ->select('rar_id, rar_name, rar_tag')
            ->orderBy('rar_name')
            ->get()
            ->getResult();
    }

    public function getRarsAndGroups(): array
    {
        $sql = '
            SELECT rar_id, rar_name, rar_tag
            FROM rar
            UNION
            SELECT rar_group_id AS rar_id, rar_group_name AS rar_name, \'XX\' AS rar_tag
            FROM rar_group
            ORDER BY rar_name
        ';

        return $this->db->query($sql)->getResult();
    }
}
