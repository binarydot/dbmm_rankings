<?php

namespace App\Controllers;

use App\Models\CompetitionMatchesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CompetitionMatches extends BaseController
{
    public function show(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $competitionId = (int) $id;
        $model = new CompetitionMatchesModel();
        $competition = $model->getCompetition($competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        $hasSeriesAccess = isset($seriesPermissions[$competition['competition_series_id']]);

        return view('competitions/matches', [
            'title' => session()->get('site_lang') === 'italian' ? 'Partite del concorso' : 'Competition Matches',
            'menuItem' => 'competitions',
            'siteLanguage' => session()->get('site_lang') === 'italian' ? 'italian' : 'english',
            'competition' => $competition,
            'rounds' => $model->getRounds($competitionId),
            'canAddMatch' => ($permissions['Add Matches'] ?? 0) == 1 && $hasSeriesAccess,
            'canManageMatches' => ($permissions['Update Matches'] ?? 0) == 1 && $hasSeriesAccess,
        ]);
    }
}
