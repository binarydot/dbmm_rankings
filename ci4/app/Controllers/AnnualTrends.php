<?php

namespace App\Controllers;

use App\Models\AnnualTrendsModel;

class AnnualTrends extends BaseController
{
    public function index(): string
    {
        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('matches/annual-trends', [
            'title' => $siteLanguage === 'italian' ? 'Andamento annuale' : 'Annual Trends',
            'menuItem' => 'stats',
            'siteLanguage' => $siteLanguage,
            'trends' => (new AnnualTrendsModel())->getTrends(),
        ]);
    }
}
