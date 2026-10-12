<?php

namespace App\Controllers;

use App\Models\PlayerDirectoryModel;
use App\Models\PlayerResultsModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PlayerResults extends BaseController
{
    public function show(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $playerId = (int) $id;
        $player = (new PlayerDirectoryModel())->getPlayer($playerId);
        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player does not exist.');
        }

        $language = session()->get('site_lang') === 'italian' ? 'italian' : 'english';
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        return view('players/results', [
            'title' => $language === 'italian' ? 'Risultati del giocatore' : 'Player Results',
            'menuItem' => 'players',
            'player' => $player,
            'results' => (new PlayerResultsModel())->getHistory($playerId),
            'siteLanguage' => $language,
            'canAddPlayer' => ($permissions['Add Player'] ?? 0) == 1,
            'canUpdatePlayer' => ($permissions['Update Player'] ?? 0) == 1,
        ]);
    }
}
