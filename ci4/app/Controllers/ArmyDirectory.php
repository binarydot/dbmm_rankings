<?php

namespace App\Controllers;

use App\Models\ArmyDirectoryModel;

class ArmyDirectory extends BaseController
{
    public function index(): string
    {
        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('armies/index', [
            'title' => $siteLanguage === 'italian' ? 'Eserciti' : 'Armies',
            'menuItem' => 'armies',
            'siteLanguage' => $siteLanguage,
            'armies' => (new ArmyDirectoryModel())->getActiveArmies(),
        ]);
    }
}
