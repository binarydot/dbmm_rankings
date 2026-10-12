<?php

namespace App\Controllers;

use App\Models\ArmyDirectoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ArmyDetails extends BaseController
{
    public function show(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $army = (new ArmyDirectoryModel())->getArmy((int) $id);
        if ($army === null) {
            throw PageNotFoundException::forPageNotFound('Army does not exist.');
        }

        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('armies/details', [
            'title' => $siteLanguage === 'italian' ? 'Dettagli esercito' : 'Army Details',
            'menuItem' => 'armies',
            'siteLanguage' => $siteLanguage,
            'army' => $army,
        ]);
    }
}
