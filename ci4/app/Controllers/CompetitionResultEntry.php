<?php

namespace App\Controllers;

use App\Models\CompetitionResultEntryModel;
use App\Models\CompetitionResultsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CompetitionResultEntry extends BaseController
{
    public function add(string $competitionId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        return view('competitions/result-add', [
            'title' => 'Add Competition Result',
            'menuItem' => 'competitions',
            'competition' => $competition,
            ... (new CompetitionResultEntryModel())->getEntryOptions(),
        ]);
    }

    public function insert(string $competitionId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $data = $this->postedResult();
        $validation = service('validation');
        if (! $validation->run($data, $this->resultRules())) {
            return redirect()->to(site_url('competition/addResult/' . $competitionId))
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        if ($data['competitor_id_2'] !== null && ! in_array($competition['type'], ['Mixed', 'Doubles'], true)) {
            return redirect()->to(site_url('competition/addResult/' . $competitionId))
                ->withInput()
                ->with('error', 'A second competitor is only valid for a team competition.');
        }

        $model = new CompetitionResultEntryModel();
        if (! $model->selectionsExist($data['competitor_id'], $data['competitor_id_2'], $data['army_id'])) {
            return redirect()->to(site_url('competition/addResult/' . $competitionId))
                ->withInput()
                ->with('error', 'Select valid competitors and an active army.');
        }

        if (! $model->insertResult(
            (int) $competitionId,
            $data['competitor_id'],
            $data['competitor_id_2'],
            $data['army_id'],
            (float) $data['score'],
            (int) $data['bonus'],
            (int) $data['position']
        )) {
            throw new \RuntimeException('Unable to add competition result.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    public function edit(string $competitionId, string $positionId): string|RedirectResponse
    {
        $this->validatePositionId($positionId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $model = new CompetitionResultEntryModel();
        $result = $model->getResult((int) $competitionId, (int) $positionId);
        if ($result === null) {
            throw PageNotFoundException::forPageNotFound('Competition result does not exist.');
        }

        $partnerId = null;
        if (count($result['member_ids']) === 2) {
            $partnerId = $result['member_ids'][0] === (int) $result['competitor_id']
                ? $result['member_ids'][1]
                : $result['member_ids'][0];
        }

        return view('competitions/result-edit', [
            'title' => 'Edit Competition Result',
            'menuItem' => 'competitions',
            'competition' => $competition,
            'result' => $result,
            'partnerId' => $partnerId,
            ...$model->getEntryOptions(),
        ]);
    }

    public function update(string $competitionId, string $positionId): RedirectResponse
    {
        $this->validatePositionId($positionId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $model = new CompetitionResultEntryModel();
        $result = $model->getResult((int) $competitionId, (int) $positionId);
        if ($result === null) {
            throw PageNotFoundException::forPageNotFound('Competition result does not exist.');
        }

        $data = $this->postedResult();
        if (count($result['member_ids']) !== 2) {
            $data['competitor_id_2'] = null;
        }
        $validation = service('validation');
        if (! $validation->run($data, $this->resultRules())) {
            return redirect()->to(site_url('competition/editResult/' . $competitionId . '/' . $positionId))
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        if (! $model->selectionsExist($data['competitor_id'], $data['competitor_id_2'], $data['army_id'])) {
            return redirect()->to(site_url('competition/editResult/' . $competitionId . '/' . $positionId))
                ->withInput()
                ->with('error', 'Select valid competitors and an active army.');
        }

        if (! $model->updateResult(
            (int) $competitionId,
            (int) $positionId,
            $data['competitor_id'],
            $data['competitor_id_2'],
            $data['army_id'],
            (float) $data['score'],
            (int) $data['bonus'],
            (int) $data['position']
        )) {
            throw new \RuntimeException('Unable to update competition result.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    public function confirmDelete(string $competitionId, string $positionId): string|RedirectResponse
    {
        $this->validatePositionId($positionId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $result = (new CompetitionResultEntryModel())->getResult((int) $competitionId, (int) $positionId);
        if ($result === null) {
            throw PageNotFoundException::forPageNotFound('Competition result does not exist.');
        }

        return view('competitions/result-delete', [
            'title' => 'Delete Competition Result',
            'menuItem' => 'competitions',
            'competition' => $competition,
            'result' => $result,
        ]);
    }

    public function delete(string $competitionId, string $positionId): RedirectResponse
    {
        $this->validatePositionId($positionId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Result');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        if (! (new CompetitionResultEntryModel())->deleteResult((int) $competitionId, (int) $positionId)) {
            throw PageNotFoundException::forPageNotFound('Competition result does not exist or could not be deleted.');
        }

        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    private function authorizedCompetition(string $competitionId, string $permission): array|RedirectResponse
    {
        if (! ctype_digit($competitionId) || (int) $competitionId < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $competition = (new CompetitionResultsModel())->getCompetition((int) $competitionId);
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

    private function validatePositionId(string $positionId): void
    {
        if (! ctype_digit($positionId) || (int) $positionId < 1) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    private function postedResult(): array
    {
        $post = (array) service('request')->getPost();
        $scalar = static fn ($value) => is_scalar($value) ? (string) $value : '';
        $competitor2 = $scalar($post['competitorId2'] ?? '-1');
        $armyId = $scalar($post['army'] ?? '');

        return [
            'competitor_id' => $scalar($post['competitorId'] ?? ''),
            'competitor_id_2' => $competitor2 === '' || $competitor2 === '-1' ? null : $competitor2,
            'army_id' => $armyId === '' || $armyId === '-1' ? null : $armyId,
            'position' => $scalar($post['position'] ?? ''),
            'score' => $scalar($post['score'] ?? ''),
            'bonus' => $scalar($post['bonus'] ?? '0') === '' ? '0' : $scalar($post['bonus'] ?? '0'),
        ];
    }

    private function resultRules(): array
    {
        return [
            'competitor_id' => 'required|is_natural_no_zero',
            'competitor_id_2' => 'permit_empty|is_natural_no_zero',
            'army_id' => 'permit_empty|is_natural_no_zero',
            'position' => 'required|is_natural_no_zero',
            'score' => 'required|numeric',
            'bonus' => 'required|integer',
        ];
    }
}
