<?php

namespace App\Controllers;

use App\Models\CompetitionResultsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CompetitionResults extends BaseController
{
    public function show(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $competitionId = (int) $id;
        $model = new CompetitionResultsModel();
        $competition = $model->getCompetition($competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $fullResults = $model->hasResults($competitionId);
        $resultData = $model->getResults($competitionId, $fullResults, (float) $competition['weighting']);
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        $hasSeriesAccess = isset($seriesPermissions[$competition['competition_series_id']]);
        $canAddResult = ($permissions['Add Result'] ?? 0) == 1 && $hasSeriesAccess;
        $roundStandings = $fullResults ? $model->getRoundStandings($competitionId) : ['rounds' => [], 'players' => []];

        return view('competitions/results', [
            'title' => session()->get('site_lang') === 'italian' ? 'Risultati del concorso' : 'Competition Results',
            'menuItem' => 'competitions',
            'siteLanguage' => session()->get('site_lang') === 'italian' ? 'italian' : 'english',
            'competition' => $competition,
            'fullResults' => $fullResults,
            'results' => $resultData['rows'],
            'totalRating' => $resultData['totalRating'],
            'umpires' => $model->getUmpires($competitionId),
            'standIns' => $model->getStandIns($competitionId),
            'roundStandings' => $roundStandings,
            'canAddResult' => $canAddResult,
            'canUpdateResult' => ($permissions['Update Result'] ?? 0) == 1 && $hasSeriesAccess,
        ]);
    }
}
