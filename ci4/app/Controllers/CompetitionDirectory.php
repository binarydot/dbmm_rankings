<?php

namespace App\Controllers;

use App\Models\CompetitionDirectoryModel;

class CompetitionDirectory extends BaseController
{
    public function index(): string
    {
        $request = service('request');
        $regionId = $this->filterId($request->getPost('rar'), 0);
        $seriesId = $this->filterId($request->getPost('series'), 0);
        $year = $this->filterYear($request->getPost('year'));

        if ($request->getMethod() !== 'POST') {
            $regionId = 0;
            $seriesId = 0;
            $year = -1;
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        $loggedIn = (bool) session()->get('loggedIn');
        $model = new CompetitionDirectoryModel();

        return view('competitions/index', [
            'title' => session()->get('site_lang') === 'italian' ? 'Concorsi' : 'Competitions',
            'menuItem' => 'competitions',
            'siteLanguage' => session()->get('site_lang') === 'italian' ? 'italian' : 'english',
            'regions' => $model->getRegions(),
            'series' => $model->getSeries(),
            'competitions' => $model->getCompetitions($regionId, $seriesId, $year),
            'regionId' => $regionId,
            'seriesId' => $seriesId,
            'year' => $year,
            'canAddCompetition' => ($permissions['Add Competition'] ?? 0) == 1,
            'canUpdateCompetition' => ($permissions['Update Competition'] ?? 0) == 1,
            'seriesPermissions' => $seriesPermissions,
            'loggedIn' => $loggedIn,
        ]);
    }

    private function filterId(mixed $value, int $default): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return is_scalar($value) && ctype_digit((string) $value) ? (int) $value : $default;
    }

    private function filterYear(mixed $value): int
    {
        if ($value === null || $value === '') {
            return -1;
        }

        if (! is_scalar($value) || ! preg_match('/^-?\d+$/', (string) $value)) {
            return -1;
        }

        $year = (int) $value;
        if ($year === -1 || $year === 0) {
            return $year;
        }

        return $year >= 2007 && $year <= (int) date('Y') ? $year : -1;
    }
}
