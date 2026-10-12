<?php

namespace App\Controllers;

use App\Models\ArmyStatisticsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ArmyStatistics extends BaseController
{
    public function index(): string
    {
        return $this->renderStatistics('booklistorder', '12', 'none');
    }

    public function show(string $viewType, string $monthsBack = '0', string $rankingMethod = 'none'): string
    {
        if (! in_array($viewType, ['mostused', 'booklistorder', 'firstused'], true)
            || ! in_array($monthsBack, ['0', '12'], true)
            || $rankingMethod !== 'none') {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->renderStatistics($viewType, $monthsBack, $rankingMethod);
    }

    private function renderStatistics(string $viewType, string $monthsBack, string $rankingMethod): string
    {
        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('armies/statistics', [
            'title' => $siteLanguage === 'italian' ? 'Statistiche eserciti' : 'Army Statistics',
            'menuItem' => 'armies',
            'siteLanguage' => $siteLanguage,
            'viewType' => $viewType,
            'monthsBack' => (int) $monthsBack,
            'rankingMethod' => $rankingMethod,
            'armies' => (new ArmyStatisticsModel())->getStatistics($viewType, (int) $monthsBack),
        ]);
    }
}
