<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionDetailsModel extends Model
{
    protected $table = 'competition';
    protected $primaryKey = 'competition_id';

    public function getDetails(int $competitionId): ?array
    {
        return $this->db->table('competition c')
            ->select('c.competition_id, c.competition_name, c.competition_date, c.type, c.weighting, c.protected, c.glicko_eligible, c.competition_series_id, cs.competition_series_name, c.rar_id, r.rar_name, c.system_id, s.system_name, s.version, c.scale_id, sc.desc AS scale_desc, c.army_points, c.location, c.lists_allowed, c.rounds, c.num_competitors, c.scoring_system_id, ss.desc AS scoring_system_desc, c.format_id, f.desc AS format_desc, c.theme_id, t.desc AS theme_desc, c.notes')
            ->join('competition_series cs', 'cs.competition_series_id = c.competition_series_id')
            ->join('rar r', 'r.rar_id = c.rar_id')
            ->join('system s', 's.system_id = c.system_id')
            ->join('scale sc', 'sc.scale_id = c.scale_id')
            ->join('scoring_system ss', 'ss.scoring_system_id = c.scoring_system_id')
            ->join('theme t', 't.theme_id = c.theme_id')
            ->join('format f', 'f.format_id = c.format_id')
            ->where('c.competition_id', $competitionId)
            ->get()
            ->getRowArray();
    }
}
