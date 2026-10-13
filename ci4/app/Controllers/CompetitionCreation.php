<?php

namespace App\Controllers;

use App\Models\CompetitionCreationModel;
use CodeIgniter\HTTP\RedirectResponse;

class CompetitionCreation extends BaseController
{
    public function add(): string|RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];

        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (($permissions['Add Competition'] ?? 0) != 1) {
            return redirect()->to(site_url('competition'));
        }

        $options = (new CompetitionCreationModel())->getFormOptions();
        $options['series'] = array_values(array_filter(
            $options['series'],
            static fn (object $series): bool => isset($seriesPermissions[$series->id])
        ));

        if ($options['series'] === []) {
            return redirect()->to(site_url('competition'));
        }

        return view('competitions/add', [
            'title' => 'Add Competition',
            'menuItem' => 'competitions',
            ...$options,
        ]);
    }

    public function insert(): RedirectResponse
    {
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        if (($permissions['Add Competition'] ?? 0) != 1) {
            return redirect()->to(site_url('competition'));
        }

        $data = CompetitionCreationModel::formData((array) service('request')->getPost());
        $validation = service('validation');
        $validation->setRules(CompetitionCreationModel::validationRules());
        if (! $validation->run($data)) {
            return redirect()->to(site_url('competition/add'))
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        $seriesId = (int) $data['competition_series_id'];
        if (! isset($seriesPermissions[$seriesId])) {
            return redirect()->to(site_url('competition/add'))
                ->withInput()
                ->with('error', 'You do not have permission to add a competition to that series.');
        }

        $competitionId = (new CompetitionCreationModel())->insertCompetition($data);
        if ($competitionId === false) {
            throw new \RuntimeException('Unable to create competition.');
        }

        return redirect()->to(site_url('competition/details/' . $competitionId));
    }
}
