<?php

namespace App\Controllers;

use App\Models\PlayerDirectoryModel;
use App\Models\PlayerManagementModel;
use App\Models\RarModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class PlayerManagement extends BaseController
{
    public function insert(): RedirectResponse
    {
        $permission = $this->authorized('Add Player');
        if ($permission instanceof RedirectResponse) {
            return $permission;
        }

        $data = $this->playerData();
        $model = new PlayerManagementModel();
        $errors = $this->validatePlayer($data, $model);
        if ($errors !== []) {
            return redirect()->to(site_url('player/add'))->withInput()->with('errors', $errors);
        }

        $playerId = $model->insertPlayer($data);
        if ($playerId === false) {
            throw new \RuntimeException('Unable to add player.');
        }

        return redirect()->to(site_url('player/details/' . $playerId));
    }

    public function update(string $id): RedirectResponse
    {
        $permission = $this->authorized('Update Player');
        if ($permission instanceof RedirectResponse) {
            return $permission;
        }
        $playerId = $this->validId($id);

        $directory = new PlayerDirectoryModel();
        if ($directory->getPlayer($playerId) === null) {
            throw PageNotFoundException::forPageNotFound('Player does not exist.');
        }

        $data = $this->playerData();
        $model = new PlayerManagementModel();
        $errors = $this->validatePlayer($data, $model);
        if ($errors !== []) {
            return redirect()->to(site_url('player/edit/' . $playerId))->withInput()->with('errors', $errors);
        }
        if (! $model->updatePlayer($playerId, $data)) {
            throw new \RuntimeException('Unable to update player.');
        }

        return redirect()->to(site_url('player/details/' . $playerId));
    }

    public function delete(string $id): RedirectResponse
    {
        $permission = $this->authorized('Update Player');
        if ($permission instanceof RedirectResponse) {
            return $permission;
        }
        $playerId = $this->validId($id);
        $directory = new PlayerDirectoryModel();
        $player = $directory->getPlayer($playerId);
        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player does not exist.');
        }

        $model = new PlayerManagementModel();
        if (! $model->canDeletePlayer($playerId)) {
            return redirect()->to(site_url('player/details/' . $playerId))
                ->with('error', 'This player has competition, match, umpire, stand-in, or ranking history and cannot be deleted.');
        }
        if (! $model->deletePlayer($playerId)) {
            throw new \RuntimeException('Unable to delete player.');
        }

        return redirect()->to(site_url('player/playerList'));
    }

    private function playerData(): array
    {
        $post = (array) $this->request->getPost();
        return [
            'first_name' => is_scalar($post['first_name'] ?? null) ? trim((string) $post['first_name']) : '',
            'last_name' => is_scalar($post['last_name'] ?? null) ? trim((string) $post['last_name']) : '',
            'iwf_id' => is_scalar($post['iwf_id'] ?? null) ? trim((string) $post['iwf_id']) : '',
            'rar_id' => $this->postedInteger($post['rar'] ?? null),
            'anonymous' => $this->postedInteger($post['anonymous'] ?? '0'),
        ];
    }

    private function validatePlayer(array $data, PlayerManagementModel $model): array
    {
        $errors = [];
        if ($data['first_name'] === '' || mb_strlen($data['first_name']) > 20) {
            $errors['first_name'] = 'First name is required and must be no more than 20 characters.';
        }
        if ($data['last_name'] === '' || mb_strlen($data['last_name']) > 60) {
            $errors['last_name'] = 'Last name is required and must be no more than 60 characters.';
        }
        if (mb_strlen($data['iwf_id']) > 7) {
            $errors['iwf_id'] = 'IWF ID must be no more than 7 characters.';
        }
        if ($data['rar_id'] === null || $data['rar_id'] < 1 || ! $model->regionExists($data['rar_id'])) {
            $errors['rar'] = 'Select a valid region.';
        }
        if (! in_array($data['anonymous'], [0, 1], true)) {
            $errors['anonymous'] = 'Select a valid anonymous setting.';
        }

        return $errors;
    }

    private function postedInteger(mixed $value): ?int
    {
        if (! is_scalar($value) || ! preg_match('/^-?\d+$/', (string) $value)) {
            return null;
        }

        return (int) $value;
    }

    private function authorized(string $permission): ?RedirectResponse
    {
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        if (($permissions[$permission] ?? 0) != 1) {
            return redirect()->to(site_url('player'));
        }

        return null;
    }

    private function validId(string $id): int
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (int) $id;
    }
}
