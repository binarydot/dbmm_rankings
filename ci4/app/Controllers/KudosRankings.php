<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class KudosRankings extends BaseController
{
    public function index(): string
    {
        $request = service('request');
        $year = (string) ($request->getGet('year') ?? '-1');
        $rar = (string) ($request->getGet('rar') ?? '-1');

        return $this->render($year, $rar);
    }

    public function show(string $year, string $rar = '-1'): string
    {
        return $this->render($year, $rar);
    }

    private function render(string $year, string $rar): string
    {
        if ($year !== '-1' && (! ctype_digit($year) || (int) $year <= 2000 || (int) $year > (int) date('Y'))) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($rar !== '-1' && (! ctype_digit($rar) || (int) $rar < 0)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->load->model('Kudos_rankings_model');
        $this->load->model('Rar_model');
        $rankingsData = $this->Kudos_rankings_model->getRankings($year, $rar);
        $rankings = array_values(array_filter(
            $rankingsData['rankings'] ?? [],
            static fn (array $ranking): bool => (int) ($ranking['playerId'] ?? 0) > 0
        ));
        usort($rankings, static fn (array $left, array $right): int => (float) $right['playerData']['ranking'] <=> (float) $left['playerData']['ranking']);
        $siteLanguage = session()->get('site_lang') === 'italian' ? 'italian' : 'english';

        return view('rankings/kudos', [
            'title' => 'Kudos Rankings',
            'menuItem' => 'kudos',
            'siteLanguage' => $siteLanguage,
            'rankingsData' => $rankingsData,
            'rankings' => $rankings,
            'regions' => $this->Rar_model->getRars(),
            'selectedYear' => $year,
            'selectedRar' => (int) $rar,
            'years' => range((int) date('Y'), 2001),
        ]);
    }
}
