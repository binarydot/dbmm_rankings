<?php

namespace App\Controllers;

use App\Models\CompetitionDetailsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CompetitionDetails extends BaseController
{
    public function show(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $competitionId = (int) $id;
        $model = new CompetitionDetailsModel();
        $competition = $model->getDetails($competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];

        return view('competitions/details', [
            'title' => session()->get('site_lang') === 'italian' ? 'Dettagli concorso' : 'Competition Details',
            'menuItem' => 'competitions',
            'siteLanguage' => session()->get('site_lang') === 'italian' ? 'italian' : 'english',
            'competition' => $competition,
            'canUpdate' => ($permissions['Update Competition'] ?? 0) == 1
                && isset($seriesPermissions[$competition['competition_series_id']]),
        ]);
    }
}
