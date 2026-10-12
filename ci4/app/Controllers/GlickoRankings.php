<?php

namespace App\Controllers;

use App\Models\GlickoRankingsModel;

class GlickoRankings extends BaseController
{
    public function index(): string
    {
        $request = service('request');
        $reliability = (string) ($request->getGet('reliability') ?? 'C');
        $rar = (string) ($request->getGet('rar') ?? '-1');
        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        $model = new GlickoRankingsModel();
        $data = $model->getRankings($reliability, $rar);

        return view('rankings/glicko', [
            'title' => $siteLanguage === 'italian' ? 'Classifica Glicko' : 'Glicko Rankings',
            'menuItem' => 'glicko',
            'siteLanguage' => $siteLanguage,
            'reliabilities' => $model->getReliabilityOptions(),
            'regions' => $model->getRegions(),
            'rarUsed' => $data['rarUsed'],
            'reliabilityUsed' => $data['reliabilityUsed'],
            'thresholds' => $data['thresholds'],
            'rankings' => $data['rankings'],
            'showReliabilityValue' => (bool) session()->get('See All Glicko'),
        ]);
    }
}
