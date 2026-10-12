<?php

namespace App\Controllers;

use App\Models\CompetitionCreationModel;
use App\Models\CompetitionDetailsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CompetitionEdit extends BaseController
{
    public function show(string $id): string|RedirectResponse
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        if (($permissions['Update Competition'] ?? 0) != 1) {
            return redirect()->to(site_url('competition'));
        }

        $competition = (new CompetitionDetailsModel())->getDetails((int) $id);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        if (! isset($seriesPermissions[$competition['competition_series_id']])) {
            return redirect()->to(site_url('competition'));
        }

        $options = (new CompetitionCreationModel())->getFormOptions();
        $options['series'] = array_values(array_filter(
            $options['series'],
            static fn (object $series): bool => isset($seriesPermissions[$series->id])
        ));

        return view('competitions/edit', [
            'title' => 'Edit Competition',
            'menuItem' => 'competitions',
            'competition' => $competition,
            ...$options,
        ]);
    }

    public function update(string $id): RedirectResponse
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        if (($permissions['Update Competition'] ?? 0) != 1) {
            return redirect()->to(site_url('competition'));
        }

        $competitionId = (int) $id;
        $detailsModel = new CompetitionDetailsModel();
        $competition = $detailsModel->getDetails($competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        if (! isset($seriesPermissions[$competition['competition_series_id']])) {
            return redirect()->to(site_url('competition'));
        }

        $data = CompetitionCreationModel::formData((array) service('request')->getPost());
        $validation = service('validation');
        if (! $validation->run($data, CompetitionCreationModel::validationRules())) {
            return redirect()->to(site_url('competition/edit/' . $competitionId))
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $newSeriesId = (int) $data['competition_series_id'];
        if (! isset($seriesPermissions[$newSeriesId])) {
            return redirect()->to(site_url('competition/edit/' . $competitionId))
                ->withInput()
                ->with('error', 'You do not have permission to move this competition to that series.');
        }

        if (! (new CompetitionCreationModel())->updateCompetition($competitionId, $data)) {
            throw new \RuntimeException('Unable to update competition.');
        }

        return redirect()->to(site_url('competition/details/' . $competitionId));
    }

    public function deleteConfirmation(string $id): string|RedirectResponse
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        if (($permissions['Update Competition'] ?? 0) != 1) {
            return redirect()->to(site_url('competition'));
        }

        $competition = (new CompetitionDetailsModel())->getDetails((int) $id);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        if (! isset($seriesPermissions[$competition['competition_series_id']])) {
            return redirect()->to(site_url('competition'));
        }

        return view('competitions/delete', [
            'title' => 'Delete Competition',
            'menuItem' => 'competitions',
            'competition' => $competition,
        ]);
    }

    public function delete(string $id): RedirectResponse
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        if (($permissions['Update Competition'] ?? 0) != 1) {
            return redirect()->to(site_url('competition'));
        }

        $competitionId = (int) $id;
        $competition = (new CompetitionDetailsModel())->getDetails($competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        if (! isset($seriesPermissions[$competition['competition_series_id']])) {
            return redirect()->to(site_url('competition'));
        }

        if (! (new CompetitionCreationModel())->deleteCompetition($competitionId)) {
            throw new \RuntimeException('Unable to delete competition.');
        }

        return redirect()->to(site_url('competition'));
    }
}
