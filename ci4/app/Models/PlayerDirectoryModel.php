<?php

namespace App\Models;

use CodeIgniter\Model;

class PlayerDirectoryModel extends Model
{
    protected $table = 'competitor';
    protected $primaryKey = 'competitor_id';

    public function getInitials(): array
    {
        return $this->db
            ->query('SELECT DISTINCT LEFT(last_name, 1) AS initial FROM competitor ORDER BY initial')
            ->getResultArray();
    }

    public function getPlayer(int $competitorId): ?array
    {
        return $this->db->table('competitor c')
            ->select('c.competitor_id, c.first_name, c.last_name, c.cr_date, c.iwf_id, c.rar_id, c.anonymous, r.rar_name')
            ->join('rar r', 'r.rar_id = c.rar_id')
            ->where('c.competitor_id', $competitorId)
            ->get()
            ->getRowArray();
    }

    public function getPlayerArmies(int $competitorId): array
    {
        return $this->db->table('position p')
            ->select('a.name, a.army_id, COUNT(*) AS uses')
            ->join('armies a', 'a.army_id = p.army_id')
            ->where('p.competitor_id', $competitorId)
            ->groupBy(['a.name', 'a.army_id'])
            ->orderBy('uses', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getPlayersByInitial(string $initial): array
    {
        return $this->db->query(
            'SELECT c.competitor_id,
                    c.first_name,
                    c.last_name,
                    c.iwf_id,
                    c.cr_date,
                    c.anonymous,
                    r.rar_name,
                    r.rar_tag,
                    COALESCE(stats.best_place, 0) AS best_place,
                    COALESCE(stats.worst_place, 0) AS worst_place,
                    COALESCE(stats.num_comps, 0) AS num_comps,
                    COALESCE(armies.num_armies, 0) AS num_armies,
                    (
                        SELECT MAX(field_positions.position)
                        FROM position field_positions
                        WHERE field_positions.competition_id IN (
                            SELECT best_positions.competition_id
                            FROM position best_positions
                            WHERE best_positions.competitor_id = c.competitor_id
                              AND best_positions.position = stats.best_place
                        )
                    ) AS best_place_from,
                    (
                        SELECT MAX(field_positions.position)
                        FROM position field_positions
                        WHERE field_positions.competition_id IN (
                            SELECT worst_positions.competition_id
                            FROM position worst_positions
                            WHERE worst_positions.competitor_id = c.competitor_id
                              AND worst_positions.position = stats.worst_place
                        )
                    ) AS worst_place_from
             FROM competitor c
             JOIN rar r ON r.rar_id = c.rar_id
             LEFT JOIN (
                 SELECT competitor_id,
                        MIN(position) AS best_place,
                        MAX(position) AS worst_place,
                        COUNT(*) AS num_comps
                 FROM position
                 WHERE position > 0
                 GROUP BY competitor_id
             ) stats ON stats.competitor_id = c.competitor_id
             LEFT JOIN (
                 SELECT competitor_id, COUNT(DISTINCT army_id) AS num_armies
                 FROM position
                 GROUP BY competitor_id
             ) armies ON armies.competitor_id = c.competitor_id
             WHERE c.last_name LIKE ?
             ORDER BY c.last_name, c.first_name',
            [$initial . '%']
        )->getResultArray();
    }

    public function getTotalPlayers(int $rarId): int
    {
        $builder = $this->db->table('competitor c')
            ->join('rar r', 'r.rar_id = c.rar_id', 'inner');
        if ($rarId > 0) {
            $builder->where('c.rar_id', $rarId);
        }

        return (int) $builder->countAllResults();
    }

    public function getPlayersPage(int $rarId, int $pageNumber, int $pageSize): array
    {
        $builder = $this->db->table('competitor c')
            ->select([
                'c.competitor_id',
                'c.first_name',
                'c.last_name',
                'c.iwf_id',
                'c.cr_date',
                'c.anonymous',
                'r.rar_name',
                'COALESCE(stats.best_place, 0) AS best_place',
                'COALESCE(stats.worst_place, 0) AS worst_place',
                'COALESCE(stats.num_comps, 0) AS num_comps',
                'COALESCE(armies.num_armies, 0) AS num_armies',
            ])
            ->join('rar r', 'r.rar_id = c.rar_id', 'inner')
            ->join(
                '(SELECT competitor_id, MIN(position) AS best_place, MAX(position) AS worst_place, COUNT(*) AS num_comps
                  FROM position WHERE position > 0 GROUP BY competitor_id) stats',
                'stats.competitor_id = c.competitor_id',
                'left',
                false
            )
            ->join(
                '(SELECT competitor_id, COUNT(DISTINCT army_id) AS num_armies
                  FROM position GROUP BY competitor_id) armies',
                'armies.competitor_id = c.competitor_id',
                'left',
                false
            );

        if ($rarId > 0) {
            $builder->where('c.rar_id', $rarId);
        }

        return $builder
            ->orderBy('c.last_name')
            ->orderBy('c.first_name')
            ->limit($pageSize, ($pageNumber - 1) * $pageSize)
            ->get()
            ->getResultArray();
    }
}
