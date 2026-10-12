<?php

namespace App\Controllers;

use App\Models\PlayerDirectoryModel;
use App\Models\PlayerMatchesModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PlayerMatches extends BaseController
{
    public function show(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $playerId = (int) $id;
        $directory = new PlayerDirectoryModel();
        $player = $directory->getPlayer($playerId);
        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player does not exist.');
        }

        $language = session()->get('site_lang') === 'italian' ? 'italian' : 'english';
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        return view('players/matches', [
            'title' => $language === 'italian' ? 'Giocatore Partite' : 'Player Matches',
            'menuItem' => 'players',
            'player' => $player,
            'playerId' => $playerId,
            'matches' => (new PlayerMatchesModel())->getHistory($playerId),
            'siteLanguage' => $language,
            'canAddPlayer' => ($permissions['Add Player'] ?? 0) == 1,
            'canUpdatePlayer' => ($permissions['Update Player'] ?? 0) == 1,
        ]);
    }
}
