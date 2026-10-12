<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class ArmyTopDog extends BaseController
{
    public function show(string $viewType, string $monthsBack, string $rankingMethod): string
    {
        if (! in_array($viewType, ['mostused', 'booklistorder', 'firstused'], true)
            || ! in_array($monthsBack, ['0', '12'], true)
            || $rankingMethod !== 'none') {
            throw PageNotFoundException::forPageNotFound();
        }

        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('armies/topdog', [
            'title' => 'Top Dog',
            'menuItem' => 'armies',
            'siteLanguage' => $siteLanguage,
            'viewType' => $viewType,
            'monthsBack' => (int) $monthsBack,
            'rankingMethod' => $rankingMethod,
        ]);
    }
}
