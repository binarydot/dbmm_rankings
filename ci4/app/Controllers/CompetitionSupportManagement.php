<?php

namespace App\Controllers;

use App\Models\CompetitionMatchesModel;
use App\Models\CompetitionResultsModel;
use App\Models\CompetitionSupportManagementModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CompetitionSupportManagement extends BaseController
{
    public function addRunner(string $competitionId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }
        if ((new CompetitionResultsModel())->hasResults((int) $competitionId)) {
            return redirect()->to(site_url('competition/results/' . $competitionId));
        }

        return $this->form((int) $competitionId, $competition, 'runner');
    }

    public function addUmpire(string $competitionId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        return $this->form((int) $competitionId, $competition, 'umpire');
    }

    public function addStandIn(string $competitionId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        return $this->form((int) $competitionId, $competition, 'standIn');
    }

    public function insertRunner(string $competitionId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }
        if ((new CompetitionResultsModel())->hasResults((int) $competitionId)) {
            return redirect()->to(site_url('competition/results/' . $competitionId));
        }

        $post = (array) service('request')->getPost();
        $competitorId = $this->postedId($post, 'competitorId');
        $partnerId = $this->optionalPostedId($post, 'competitorId2');
        $armyId = $this->optionalPostedId($post, 'army');
        $isTeamEvent = in_array($competition['type'], ['Mixed', 'Doubles'], true);
        $options = (new CompetitionSupportManagementModel())->getOptions((int) $competitionId);
        $validIds = array_fill_keys(array_map('intval', array_column($options['competitors'], 'competitor_id')), true);
        if ($competitorId === null || ! isset($validIds[$competitorId])
            || ! $this->validOptionalPostedId($post, 'competitorId2')
            || ! $this->validOptionalPostedId($post, 'army')
            || (! $isTeamEvent && $partnerId !== null)
            || ($partnerId !== null && ! isset($validIds[$partnerId]))
            || ! $this->validArmy($armyId, $options['armies'])) {
            return $this->invalidPost((int) $competitionId, $competition, 'runner', 'Choose valid competitors and an active army.');
        }

        if (! (new CompetitionSupportManagementModel())->insertRunner((int) $competitionId, $competitorId, $partnerId, $armyId)) {
            return $this->invalidPost((int) $competitionId, $competition, 'runner', 'The runner could not be added. Check that the competitor is not already entered.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    public function insertUmpire(string $competitionId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $competitorId = $this->postedId((array) service('request')->getPost(), 'competitorId');
        $options = (new CompetitionSupportManagementModel())->getOptions((int) $competitionId);
        $validIds = array_fill_keys(array_map('intval', array_column($options['competitors'], 'competitor_id')), true);
        if ($competitorId === null || ! isset($validIds[$competitorId])
            || ! (new CompetitionSupportManagementModel())->insertUmpire((int) $competitionId, $competitorId)) {
            return $this->invalidPost((int) $competitionId, $competition, 'umpire', 'Choose a valid player who is not already an umpire.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    public function insertStandIn(string $competitionId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $post = (array) service('request')->getPost();
        $standInId = $this->postedId($post, 'standInId');
        $stoodInForId = $this->postedId($post, 'stoodInForId');
        $options = (new CompetitionSupportManagementModel())->getOptions((int) $competitionId);
        $validCompetitorIds = array_fill_keys(array_map('intval', array_column($options['competitors'], 'competitor_id')), true);
        $validEntrantIds = array_fill_keys(array_map('intval', array_column($options['competitionPlayers'], 'competitor_id')), true);
        if ($standInId === null || $stoodInForId === null
            || ! isset($validCompetitorIds[$standInId])
            || ! isset($validEntrantIds[$stoodInForId])
            || ! (new CompetitionSupportManagementModel())->insertStandIn((int) $competitionId, $standInId, $stoodInForId)) {
            return $this->invalidPost((int) $competitionId, $competition, 'standIn', 'Choose a valid stand-in and an entered competitor.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    public function confirmDeleteUmpire(string $competitionId, string $umpireId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }
        $this->validateId($umpireId);
        $item = (new CompetitionSupportManagementModel())->getUmpire((int) $competitionId, (int) $umpireId);
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound('Competition umpire does not exist.');
        }

        return $this->deleteConfirmation($competition, $item, 'umpire', $competitionId, $umpireId);
    }

    public function confirmDeleteStandIn(string $competitionId, string $standInId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }
        $this->validateId($standInId);
        $item = (new CompetitionSupportManagementModel())->getStandIn((int) $competitionId, (int) $standInId);
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound('Competition stand-in does not exist.');
        }

        return $this->deleteConfirmation($competition, $item, 'standIn', $competitionId, $standInId);
    }

    public function deleteUmpire(string $competitionId, string $umpireId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }
        $this->validateId($umpireId);
        $model = new CompetitionSupportManagementModel();
        if ($model->getUmpire((int) $competitionId, (int) $umpireId) === null) {
            throw PageNotFoundException::forPageNotFound('Competition umpire does not exist.');
        }
        if (! $model->deleteUmpire((int) $competitionId, (int) $umpireId)) {
            throw new \RuntimeException('Unable to delete competition umpire.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    public function deleteStandIn(string $competitionId, string $standInId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }
        $this->validateId($standInId);
        $model = new CompetitionSupportManagementModel();
        if ($model->getStandIn((int) $competitionId, (int) $standInId) === null) {
            throw PageNotFoundException::forPageNotFound('Competition stand-in does not exist.');
        }
        if (! $model->deleteStandIn((int) $competitionId, (int) $standInId)) {
            throw new \RuntimeException('Unable to delete competition stand-in.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    private function form(int $competitionId, array $competition, string $type): string
    {
        return view('competitions/support-form', [
            'title' => $type === 'standIn' ? 'Add Stand-In' : 'Add ' . ucfirst($type),
            'menuItem' => 'competitions',
            'competition' => $competition,
            'type' => $type,
            'isTeamEvent' => in_array($competition['type'], ['Mixed', 'Doubles'], true),
            'options' => (new CompetitionSupportManagementModel())->getOptions($competitionId),
        ]);
    }

    private function invalidPost(int $competitionId, array $competition, string $type, string $message): RedirectResponse
    {
        return redirect()->to(site_url('competition/add' . ($type === 'standIn' ? 'StandIn' : ucfirst($type)) . '/' . $competitionId))
            ->withInput()
            ->with('error', $message);
    }

    private function deleteConfirmation(array $competition, array $item, string $type, string $competitionId, string $itemId): string
    {
        $display = $type === 'umpire'
            ? trim($item['first_name'] . ' ' . $item['last_name'])
            : trim($item['standin_first_name'] . ' ' . $item['standin_last_name']) . ' for ' . trim($item['replaced_first_name'] . ' ' . $item['replaced_last_name']);

        return view('competitions/support-delete', [
            'title' => 'Delete ' . ($type === 'umpire' ? 'Umpire' : 'Stand-In'),
            'menuItem' => 'competitions',
            'competition' => $competition,
            'display' => $display,
            'action' => site_url('competition/delete' . ($type === 'umpire' ? 'Umpire' : 'StandIn') . '/' . $competitionId . '/' . $itemId),
        ]);
    }

    private function authorizedCompetition(string $competitionId, string $permission): array|RedirectResponse
    {
        $this->validateId($competitionId);
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $competition = (new CompetitionMatchesModel())->getCompetition((int) $competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        if (($permissions[$permission] ?? 0) != 1 || ! isset($seriesPermissions[$competition['competition_series_id']])) {
            return redirect()->to(site_url('competition'));
        }

        return $competition;
    }

    private function validateId(string $id): void
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    private function postedId(array $post, string $field): ?int
    {
        $value = $post[$field] ?? null;
        return is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0 ? (int) $value : null;
    }

    private function optionalPostedId(array $post, string $field): ?int
    {
        $value = $post[$field] ?? null;
        if ($value === null || $value === '' || $value === '-1') {
            return null;
        }

        return $this->postedId($post, $field);
    }

    private function validOptionalPostedId(array $post, string $field): bool
    {
        $value = $post[$field] ?? null;
        return $value === null || $value === '' || $value === '-1'
            || (is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0);
    }

    private function validArmy(?int $armyId, array $armies): bool
    {
        if ($armyId === null) {
            return true;
        }

        return in_array($armyId, array_map('intval', array_column($armies, 'army_id')), true);
    }
}
