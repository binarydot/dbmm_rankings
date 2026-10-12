<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionResultEntryModel extends Model
{
    protected $table = 'position';
    protected $primaryKey = 'position_id';

    public function getEntryOptions(): array
    {
        return [
            'competitors' => $this->db->table('competitor c')
                ->select('c.competitor_id, c.first_name, c.last_name')
                ->join('rar r', 'r.rar_id = c.rar_id')
                ->orderBy('c.first_name')
                ->orderBy('c.last_name')
                ->get()
                ->getResultArray(),
            'armies' => $this->db->table('armies a')
                ->select('a.army_id, a.name, s.system_name, a.version')
                ->join('system s', 's.system_id = a.system_id')
                ->join('army_books ab', 'ab.book_id = a.book_id')
                ->where('ab.status', 'A')
                ->orderBy('a.name')
                ->orderBy('s.system_name')
                ->orderBy('a.version')
                ->get()
                ->getResultArray(),
        ];
    }

    public function getResult(int $competitionId, int $positionId): ?array
    {
        $row = $this->db->table('position p')
            ->select('p.position_id, p.competition_id, p.competitor_id, p.position, p.score, p.army_id, p.bonus, p.pairing')
            ->where('p.competition_id', $competitionId)
            ->where('p.position_id', $positionId)
            ->where('p.type', 'P')
            ->where('p.position >', 0)
            ->get()
            ->getRowArray();

        if ($row === null) {
            return null;
        }

        $memberIds = [(int) $row['competitor_id']];
        if ((int) $row['pairing'] > 0) {
            $memberIds = array_map(
                'intval',
                array_column(
                    $this->db->table('position')
                        ->select('competitor_id')
                        ->where('competition_id', $competitionId)
                        ->where('pairing', $row['pairing'])
                        ->where('type', 'P')
                        ->orderBy('position_id')
                        ->get()
                        ->getResultArray(),
                    'competitor_id'
                )
            );
        }

        $row['member_ids'] = $memberIds;
        return $row;
    }

    public function insertResult(int $competitionId, int $competitorId, ?int $partnerId, ?int $armyId, float $score, int $bonus, int $position): bool
    {
        $this->db->transBegin();
        $pairing = 0;
        if ($partnerId !== null) {
            $pairing = (int) ($this->db->table('position')
                ->selectMax('pairing', 'max_pairing')
                ->where('competition_id', $competitionId)
                ->get()
                ->getRowArray()['max_pairing'] ?? 0) + 1;
        }

        $members = [$competitorId];
        if ($partnerId !== null) {
            $members[] = $partnerId;
        }

        foreach ($members as $memberId) {
            if (! $this->db->table($this->table)->insert([
                'competition_id' => $competitionId,
                'competitor_id' => $memberId,
                'position' => $position,
                'score' => $score,
                'army_id' => $armyId,
                'bonus' => $bonus,
                'team' => '',
                'pairing' => $pairing,
                'type' => 'P',
            ])) {
                $this->db->transRollback();
                return false;
            }
        }

        if (! $this->db->transStatus()) {
            $this->db->transRollback();
            return false;
        }

        return $this->db->transCommit();
    }

    public function updateResult(int $competitionId, int $positionId, int $competitorId, ?int $partnerId, ?int $armyId, float $score, int $bonus, int $position): bool
    {
        $result = $this->getResult($competitionId, $positionId);
        if ($result === null) {
            return false;
        }

        $memberIds = [$competitorId];
        if ($partnerId !== null) {
            $memberIds[] = $partnerId;
        }
        if (count(array_unique($memberIds)) !== count($memberIds)) {
            return false;
        }

        $this->db->transBegin();
        if (count($result['member_ids']) === 2 && $partnerId !== null) {
            $rows = $this->db->table('position')
                ->select('position_id, competitor_id')
                ->where('competition_id', $competitionId)
                ->where('pairing', $result['pairing'])
                ->where('type', 'P')
                ->orderBy('position_id')
                ->get()
                ->getResultArray();
            if (count($rows) !== 2) {
                $this->db->transRollback();
                return false;
            }
            $targetId = (int) $result['position_id'];
            $otherId = (int) ($rows[0]['position_id'] === $targetId ? $rows[1]['position_id'] : $rows[0]['position_id']);
            $updates = [
                [$targetId, $competitorId],
                [$otherId, $partnerId],
            ];
        } elseif (count($result['member_ids']) === 1 && $partnerId === null) {
            $updates = [[$positionId, $competitorId]];
        } else {
            $this->db->transRollback();
            return false;
        }

        foreach ($updates as [$rowId, $memberId]) {
            if (! $this->db->table('position')
                ->where('position_id', $rowId)
                ->where('competition_id', $competitionId)
                ->update([
                    'competitor_id' => $memberId,
                    'position' => $position,
                    'score' => $score,
                    'army_id' => $armyId,
                    'bonus' => $bonus,
                ])) {
                $this->db->transRollback();
                return false;
            }
        }

        if (! $this->db->transStatus()) {
            $this->db->transRollback();
            return false;
        }

        return $this->db->transCommit();
    }

    public function deleteResult(int $competitionId, int $positionId): bool
    {
        $result = $this->getResult($competitionId, $positionId);
        if ($result === null) {
            return false;
        }

        $builder = $this->db->table('position')
            ->where('competition_id', $competitionId)
            ->where('type', 'P');
        if ((int) $result['pairing'] > 0) {
            $builder->where('pairing', $result['pairing']);
        } else {
            $builder->where('position_id', $positionId);
        }

        return $builder->delete();
    }

    public function selectionsExist(int $competitorId, ?int $partnerId, ?int $armyId): bool
    {
        $competitors = [$competitorId];
        if ($partnerId !== null) {
            $competitors[] = $partnerId;
        }
        if (count(array_unique($competitors)) !== count($competitors)) {
            return false;
        }

        if ($this->db->table('competitor')->whereIn('competitor_id', $competitors)->countAllResults() !== count($competitors)) {
            return false;
        }

        if ($armyId === null) {
            return true;
        }

        return $this->db->table('armies a')
            ->join('army_books ab', 'ab.book_id = a.book_id')
            ->where('a.army_id', $armyId)
            ->where('ab.status', 'A')
            ->countAllResults() === 1;
    }
}
