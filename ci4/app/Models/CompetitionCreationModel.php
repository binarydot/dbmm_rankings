<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionCreationModel extends Model
{
    protected $table = 'competition';
    protected $primaryKey = 'competition_id';

    public static function formData(array $post): array
    {
        $scalar = static fn ($value) => is_scalar($value) ? $value : null;
        $optionalNumeric = static function ($value) use ($scalar) {
            $value = $scalar($value);

            return $value === null || $value === '' ? null : $value;
        };

        return [
            'competition_name' => trim((string) ($scalar($post['name'] ?? null) ?? '')),
            'competition_date' => $scalar($post['date'] ?? null),
            'type' => $scalar($post['type'] ?? null),
            'weighting' => $optionalNumeric($post['weighting'] ?? null),
            'protected' => $scalar($post['protected'] ?? null),
            'glicko_eligible' => $scalar($post['glicko_eligible'] ?? null),
            'competition_series_id' => $scalar($post['series'] ?? null),
            'rar_id' => $scalar($post['rar'] ?? null),
            'system_id' => $scalar($post['system'] ?? null),
            'scale_id' => $scalar($post['scale'] ?? null),
            'army_points' => $optionalNumeric($post['army_points'] ?? null),
            'location' => $scalar($post['location'] ?? null),
            'lists_allowed' => $optionalNumeric($post['lists_allowed'] ?? null),
            'rounds' => $optionalNumeric($post['rounds'] ?? null),
            'num_competitors' => $optionalNumeric($post['num_competitors'] ?? null),
            'scoring_system_id' => $scalar($post['scoring_system_id'] ?? null),
            'format_id' => $scalar($post['format_id'] ?? null),
            'theme_id' => $scalar($post['theme_id'] ?? null),
            'notes' => $scalar($post['notes'] ?? null),
        ];
    }

    public static function validationRules(): array
    {
        return [
            'competition_name' => 'required',
            'competition_date' => 'required|valid_date[Y-m-d]',
            'type' => 'required|in_list[Singles,Mixed,Doubles]',
            'protected' => 'required|in_list[0,1]',
            'glicko_eligible' => 'required|in_list[0,1]',
            'competition_series_id' => 'required|is_natural_no_zero',
            'rar_id' => 'required|is_natural_no_zero',
            'system_id' => 'required|is_natural_no_zero',
            'scale_id' => 'required|is_natural_no_zero',
            'scoring_system_id' => 'required|is_natural_no_zero',
            'format_id' => 'required|is_natural_no_zero',
            'theme_id' => 'required|is_natural_no_zero',
            'weighting' => 'permit_empty|decimal',
            'army_points' => 'permit_empty|is_natural',
            'lists_allowed' => 'permit_empty|decimal',
            'rounds' => 'permit_empty|is_natural',
            'num_competitors' => 'permit_empty|is_natural',
        ];
    }

    public function getFormOptions(): array
    {
        return [
            'series' => $this->db->query(
                'SELECT competition_series_id AS id, competition_series_name AS name
                 FROM competition_series
                 ORDER BY competition_series_name'
            )->getResult(),
            'regions' => $this->db->table('rar')
                ->select('rar_id, rar_name')
                ->orderBy('rar_name')
                ->get()
                ->getResult(),
            'systems' => $this->db->table('system')
                ->select('system_id, system_name, version')
                ->orderBy('system_name')
                ->orderBy('version')
                ->get()
                ->getResult(),
            'scales' => $this->db->query('SELECT scale_id, `desc` FROM scale ORDER BY `desc`, scale_id')->getResult(),
            'scoringSystems' => $this->db->query('SELECT scoring_system_id, `desc` FROM scoring_system ORDER BY scoring_system_id')->getResult(),
            'formats' => $this->db->query('SELECT format_id, `desc` FROM format ORDER BY `desc`')->getResult(),
            'themes' => $this->db->query('SELECT theme_id, `desc` FROM theme ORDER BY theme_id')->getResult(),
        ];
    }

    public function insertCompetition(array $data): int|false
    {
        if (! $this->db->table($this->table)->insert($data)) {
            return false;
        }

        $competitionId = (int) $this->db->insertID();

        return $competitionId > 0 ? $competitionId : false;
    }

    public function updateCompetition(int $competitionId, array $data): bool
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $competitionId)
            ->update($data);
    }

    public function deleteCompetition(int $competitionId): bool
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $competitionId)
            ->delete();
    }
}
