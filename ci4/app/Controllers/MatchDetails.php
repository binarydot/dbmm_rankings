<?php

namespace App\Controllers;

use App\Models\MatchDetailsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MatchDetails extends BaseController
{
    public function show(string $year = ''): string
    {
        if ($year === '') {
            $year = date('Y');
        }
        if (! ctype_digit($year) || (int) $year < 1900 || (int) $year > (int) date('Y')) {
            throw PageNotFoundException::forPageNotFound();
        }

        $yearNumber = (int) $year;
        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('matches/details', [
            'title' => $siteLanguage === 'italian' ? 'Dettagli partite' : 'Match Details',
            'menuItem' => 'stats',
            'siteLanguage' => $siteLanguage,
            'year' => $yearNumber,
            'years' => range((int) date('Y'), 2007),
            'matches' => (new MatchDetailsModel())->getByYear($yearNumber),
        ]);
    }
}
