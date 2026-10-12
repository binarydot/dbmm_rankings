<?php

namespace App\Models;

use CodeIgniter\Model;

class GlickoRankingsModel extends Model
{
    protected $table = 'glicko_rating';
    protected $primaryKey = 'match_id';

    public function getReliabilityOptions(): array
    {
        return ['A', 'B', 'C', 'D', 'E', 'All'];
    }

    public function getRankings(string $reliability, string $rar): array
    {
        $reliabilityOptions = $this->getReliabilityOptions();
        if (! in_array($reliability, $reliabilityOptions, true)) {
            $reliability = 'C';
        }

        $rarId = ctype_digit($rar) && (int) $rar >= 0 ? (int) $rar : -1;
        $thresholds = $this->db->table('glicko_settings')
            ->select('reliability_a, reliability_b, reliability_c, reliability_d, reliability_e')
            ->limit(1)
            ->get()
            ->getRowArray();
        if ($thresholds === null) {
            throw new \RuntimeException('Glicko reliability thresholds are not configured.');
        }

        $cutoff = $reliability === 'All' ? 'e' : strtolower($reliability);
        $builder = $this->db->table('competitor p')
            ->select('p.competitor_id, p.first_name, p.last_name, r.rar_id, r.rar_name, r.rar_tag, gr.rating, gr.reliability')
            ->join('rar r', 'r.rar_id = p.rar_id')
            ->join(
                'glicko_rating gr',
                "gr.competitor_id = p.competitor_id
                 AND gr.status = 'A'
                 AND gr.rating_date = (
                     SELECT MAX(latest.rating_date)
                     FROM glicko_rating latest
                     WHERE latest.competitor_id = p.competitor_id
                       AND latest.status = 'A'
                 )",
                'inner',
                false
            )
            ->join('glicko_settings gs', '1 = 1', 'inner', false)
            ->where('gr.reliability <= gs.reliability_' . $cutoff, null, false);
        if ($rarId >= 0) {
            $builder->where('p.rar_id', $rarId);
        }

        $rows = $builder
            ->orderBy('gr.rating', 'DESC')
            ->orderBy('p.last_name')
            ->orderBy('p.first_name')
            ->get()
            ->getResultArray();

        foreach ($rows as $index => &$row) {
            $row['rank'] = $index + 1;
            $row['player_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
            $row['reliability_category'] = $this->getReliabilityCategory((float) $row['reliability'], $thresholds);
        }
        unset($row);

        return [
            'rarUsed' => $rarId,
            'reliabilityUsed' => $reliability,
            'thresholds' => $thresholds,
            'rankings' => $rows,
        ];
    }

    public function getRegions(): array
    {
        return $this->db->table('rar')
            ->select('rar_id, rar_name, rar_tag')
            ->orderBy('rar_name')
            ->get()
            ->getResultArray();
    }

    private function getReliabilityCategory(float $reliability, array $thresholds): string
    {
        foreach (['a' => 'AAAAA', 'b' => 'BBBB', 'c' => 'CCC', 'd' => 'DD', 'e' => 'E'] as $grade => $label) {
            if ($reliability <= (float) $thresholds['reliability_' . $grade]) {
                return $label;
            }
        }

        return 'X';
    }
}
