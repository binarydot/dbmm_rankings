<?php

namespace App\Controllers;

use App\Models\PlayerDirectoryModel;
use App\Models\RarModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Exceptions\PageNotFoundException;

class PlayerDirectory extends BaseController
{
    public function index(): string
    {
        return $this->playerList();
    }

    public function playerList(string $initial = 'A'): string
    {
        $directory = new PlayerDirectoryModel();
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        return view('players/index', [
            'title' => 'Player List',
            'menuItem' => 'players',
            'initial' => $initial,
            'initials' => $directory->getInitials(),
            'players' => $directory->getPlayersByInitial($initial),
            'canAddPlayer' => ($permissions['Add Player'] ?? 0) == 1,
            'canUpdatePlayer' => ($permissions['Update Player'] ?? 0) == 1,
            'siteLanguage' => session()->get('site_lang') === 'italian' ? 'italian' : 'english',
        ]);
    }

    public function fullList(string $rar = '0', string $pageNumber = '1', string $pageSize = '20'): string
    {
        $rar = (string) (service('request')->getGet('rar') ?? $rar);
        foreach ([$rar, $pageNumber, $pageSize] as $value) {
            if (! ctype_digit($value)) {
                throw PageNotFoundException::forPageNotFound();
            }
        }

        $rarId = (int) $rar;
        $page = max(1, (int) $pageNumber);
        $size = (int) $pageSize;
        if ($size < 1) {
            $size = 20;
        }
        $directory = new PlayerDirectoryModel();
        $totalPlayers = $directory->getTotalPlayers($rarId);
        $totalPages = (int) ceil($totalPlayers / $size);
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        return view('players/full', [
            'title' => 'Full Player List',
            'menuItem' => 'players',
            'rars' => (new RarModel())->getRars(),
            'rarId' => $rarId,
            'pageNumber' => $page,
            'pageSize' => $size,
            'totalPlayers' => $totalPlayers,
            'totalPages' => $totalPages,
            'players' => $directory->getPlayersPage($rarId, $page, $size),
            'canAddPlayer' => ($permissions['Add Player'] ?? 0) == 1,
            'canUpdatePlayer' => ($permissions['Update Player'] ?? 0) == 1,
        ]);
    }

    public function add(): string|RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        if (! session()->get('loggedIn') || ($permissions['Add Player'] ?? 0) != 1) {
            return redirect()->to(site_url('player'));
        }

        return view('players/add', [
            'title' => 'Add Player',
            'menuItem' => 'players',
            'rars' => (new RarModel())->getRars(),
        ]);
    }

    public function edit(string $id): string|RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        if (! session()->get('loggedIn') || ($permissions['Update Player'] ?? 0) != 1) {
            return redirect()->to(site_url('player'));
        }
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $player = (new PlayerDirectoryModel())->getPlayer((int) $id);
        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player does not exist.');
        }

        return view('players/edit', [
            'title' => 'Edit Player',
            'menuItem' => 'players',
            'player' => $player,
            'rars' => (new RarModel())->getRars(),
        ]);
    }

    public function deleteConfirmation(string $id): string|RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        if (! session()->get('loggedIn') || ($permissions['Update Player'] ?? 0) != 1) {
            return redirect()->to(site_url('player'));
        }
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $player = (new PlayerDirectoryModel())->getPlayer((int) $id);
        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player does not exist.');
        }

        return view('players/delete', [
            'title' => 'Delete Player',
            'menuItem' => 'players',
            'player' => $player,
        ]);
    }

    public function details(string $id): string
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $directory = new PlayerDirectoryModel();
        $player = $directory->getPlayer((int) $id);
        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player does not exist.');
        }

        $player['armies'] = $directory->getPlayerArmies((int) $id);
        $language = session()->get('site_lang') === 'italian' ? 'italian' : 'english';
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        return view('players/details', [
            'title' => $language === 'italian' ? 'Giocatore Dettagli' : 'Player Details',
            'menuItem' => 'players',
            'player' => $player,
            'siteLanguage' => $language,
            'canAddPlayer' => ($permissions['Add Player'] ?? 0) == 1,
            'canUpdatePlayer' => ($permissions['Update Player'] ?? 0) == 1,
        ]);
    }
}
