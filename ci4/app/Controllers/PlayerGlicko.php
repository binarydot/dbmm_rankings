<?php

namespace App\Controllers;

use App\Models\PlayerDirectoryModel;
use App\Models\PlayerGlickoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PlayerGlicko extends BaseController
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

        return view('players/glicko', [
            'title' => $language === 'italian' ? 'Glicko del giocatore' : 'Player Glicko',
            'menuItem' => 'players',
            'player' => $player,
            'history' => (new PlayerGlickoModel())->getHistory($playerId),
            'siteLanguage' => $language,
            'canAddPlayer' => ($permissions['Add Player'] ?? 0) == 1,
            'canUpdatePlayer' => ($permissions['Update Player'] ?? 0) == 1,
        ]);
    }
}
