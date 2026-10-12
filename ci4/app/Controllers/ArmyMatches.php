<?php

namespace App\Controllers;

use App\Models\ArmyMatchesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ArmyMatches extends BaseController
{
    public function show(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $armyId = (int) $id;
        $model = new ArmyMatchesModel();
        $army = $model->getArmy($armyId);
        if ($army === null) {
            throw PageNotFoundException::forPageNotFound('Army does not exist.');
        }

        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('armies/matches', [
            'title' => $siteLanguage === 'italian' ? 'Partite dell’esercito' : 'Army Matches',
            'menuItem' => 'armies',
            'siteLanguage' => $siteLanguage,
            'army' => $army,
            'matches' => $model->getArmyMatches($armyId),
        ]);
    }
}
