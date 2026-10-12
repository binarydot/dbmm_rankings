<?php

namespace App\Controllers;

use App\Models\CompRankingsModel;
use App\Models\RarModel;

class Rankings extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('rankings/comps/-1/-1'));
    }

    public function comps(string $rar = '-1', string $year = '-1'): string
    {
        $rarId = preg_match('/^-?\d+$/', $rar) === 1 ? (int) $rar : -1;
        $selectedYear = preg_match('/^\d{4}$/', $year) === 1 ? (int) $year : -1;

        $rarModel = new RarModel();
        $rars = $rarModel->getRarsAndGroups();
        $rarTags = [];
        foreach ($rars as $region) {
            $rarTags[$region->rar_name] = $region->rar_tag;
        }
        $rankingsModel = new CompRankingsModel();

        return view('rankings/comps', [
            'rars' => $rars,
            'rarTags' => $rarTags,
            'rarUsed' => $rarId,
            'yearUsed' => $selectedYear,
            'rankingsData' => $rankingsModel->getRankings($selectedYear, $rarId),
        ]);
    }
}
