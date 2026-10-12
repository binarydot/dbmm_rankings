<?php

namespace App\Models;

use CodeIgniter\Model;

class CompetitionSupportManagementModel extends Model
{
    public function getOptions(int $competitionId): array
    {
        return [
            'competitors' => $this->db->table('competitor')
                ->select('competitor_id, first_name, last_name')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
                ->getResultArray(),
            'competitionPlayers' => $this->db->query(
                "SELECT DISTINCT c.competitor_id, c.first_name, c.last_name
                 FROM (
                     SELECT competitor_id FROM position WHERE competition_id = ? AND type = 'P'
                     UNION
                     SELECT competitor_id FROM stand_in WHERE competition_id = ?
                 ) entrants
                 JOIN competitor c ON c.competitor_id = entrants.competitor_id
                 ORDER BY c.last_name, c.first_name",
                [$competitionId, $competitionId]
            )->getResultArray(),
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

    public function insertRunner(int $competitionId, int $competitorId, ?int $partnerId, ?int $armyId): bool
    {
        $memberIds = [$competitorId];
        if ($partnerId !== null) {
            $memberIds[] = $partnerId;
        }
        if (count(array_unique($memberIds)) !== count($memberIds)
            || $this->db->table('competitor')->whereIn('competitor_id', $memberIds)->countAllResults() !== count($memberIds)) {
            return false;
        }
        if ($armyId !== null && ! $this->activeArmyExists($armyId)) {
            return false;
        }
        if ($this->db->table('position')
            ->where('competition_id', $competitionId)
            ->whereIn('competitor_id', $memberIds)
            ->where('type', 'P')
            ->countAllResults() > 0) {
            return false;
        }

        $pairing = 0;
        if ($partnerId !== null) {
            $pairing = (int) ($this->db->table('position')
                ->selectMax('pairing', 'max_pairing')
                ->where('competition_id', $competitionId)
                ->get()
                ->getRowArray()['max_pairing'] ?? 0) + 1;
        }

        $this->db->transBegin();
        foreach ($memberIds as $memberId) {
            if (! $this->db->table('position')->insert([
                'competition_id' => $competitionId,
                'competitor_id' => $memberId,
                'position' => 0,
                'score' => 0,
                'army_id' => $armyId,
                'type' => 'P',
                'bonus' => 0,
                'team' => '',
                'pairing' => $pairing,
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

    public function insertUmpire(int $competitionId, int $competitorId): bool
    {
        if ($this->db->table('competitor')->where('competitor_id', $competitorId)->countAllResults() !== 1
            || $this->db->table('umpire')->where('competition_id', $competitionId)->where('competitor_id', $competitorId)->countAllResults() > 0) {
            return false;
        }

        return (bool) $this->db->table('umpire')->insert([
            'competition_id' => $competitionId,
            'competitor_id' => $competitorId,
        ]);
    }

    public function insertStandIn(int $competitionId, int $standInId, int $stoodInForId): bool
    {
        if ($standInId === $stoodInForId
            || $this->db->table('competitor')->whereIn('competitor_id', [$standInId, $stoodInForId])->countAllResults() !== 2
            || $this->db->table('position')
                ->where('competition_id', $competitionId)
                ->where('competitor_id', $stoodInForId)
                ->where('type', 'P')
                ->countAllResults() === 0
            || $this->db->table('stand_in')
                ->where('competition_id', $competitionId)
                ->where('competitor_id', $standInId)
                ->where('stood_in_for', $stoodInForId)
                ->countAllResults() > 0) {
            return false;
        }

        return (bool) $this->db->table('stand_in')->insert([
            'competition_id' => $competitionId,
            'competitor_id' => $standInId,
            'stood_in_for' => $stoodInForId,
        ]);
    }

    public function getUmpire(int $competitionId, int $umpireId): ?array
    {
        return $this->db->table('umpire u')
            ->select('u.umpire_id, c.first_name, c.last_name')
            ->join('competitor c', 'c.competitor_id = u.competitor_id')
            ->where('u.competition_id', $competitionId)
            ->where('u.umpire_id', $umpireId)
            ->get()
            ->getRowArray();
    }

    public function getStandIn(int $competitionId, int $standInId): ?array
    {
        return $this->db->table('stand_in s')
            ->select('s.stand_in_id, standin.first_name AS standin_first_name, standin.last_name AS standin_last_name, replaced.first_name AS replaced_first_name, replaced.last_name AS replaced_last_name')
            ->join('competitor standin', 'standin.competitor_id = s.competitor_id')
            ->join('competitor replaced', 'replaced.competitor_id = s.stood_in_for')
            ->where('s.competition_id', $competitionId)
            ->where('s.stand_in_id', $standInId)
            ->get()
            ->getRowArray();
    }

    public function deleteUmpire(int $competitionId, int $umpireId): bool
    {
        return $this->db->table('umpire')
            ->where('competition_id', $competitionId)
            ->where('umpire_id', $umpireId)
            ->delete()
            && $this->db->affectedRows() === 1;
    }

    public function deleteStandIn(int $competitionId, int $standInId): bool
    {
        return $this->db->table('stand_in')
            ->where('competition_id', $competitionId)
            ->where('stand_in_id', $standInId)
            ->delete()
            && $this->db->affectedRows() === 1;
    }

    private function activeArmyExists(int $armyId): bool
    {
        return $this->db->table('armies a')
            ->join('army_books ab', 'ab.book_id = a.book_id')
            ->where('a.army_id', $armyId)
            ->where('ab.status', 'A')
            ->countAllResults() === 1;
    }
}
