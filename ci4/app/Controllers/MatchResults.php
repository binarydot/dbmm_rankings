<?php

namespace App\Controllers;

use App\Models\MatchResultsModel;

class MatchResults extends BaseController
{
    public function index(): string
    {
        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';
        $model = new MatchResultsModel();

        return view('matches/results', [
            'title' => $siteLanguage === 'italian' ? 'Risultati delle partite' : 'Match Results',
            'menuItem' => 'stats',
            'siteLanguage' => $siteLanguage,
            'results' => $model->getResults(),
            'scoreLabels' => $model->getScoreLabels(),
        ]);
    }
}
