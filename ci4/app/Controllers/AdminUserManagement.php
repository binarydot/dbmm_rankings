<?php

namespace App\Controllers;

use App\Models\AdminUserManagementModel;
use App\Models\User_model;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class AdminUserManagement extends BaseController
{
    public function insert(): RedirectResponse
    {
        $permission = $this->requirePermission('Create User');
        if ($permission instanceof RedirectResponse) {
            return $permission;
        }

        $post = (array) $this->request->getPost();
        $model = new AdminUserManagementModel();
        [$user, $permissions, $series, $error] = $this->validatedUser($post, $model, null, true);
        if ($error !== null) {
            return redirect()->to(site_url('admin/addUser'))->withInput()->with('error', $error);
        }

        $userId = $model->saveUser(null, $user, $permissions, $series);
        if ($userId === false) {
            throw new \RuntimeException('Unable to create user and save permissions.');
        }

        return redirect()->to(site_url('admin/userDetails/' . $userId));
    }

    public function update(string $id): RedirectResponse
    {
        $permission = $this->requirePermission('User Menu');
        if ($permission instanceof RedirectResponse) {
            return $permission;
        }
        $userId = $this->validId($id);
        $model = new AdminUserManagementModel();
        if ((new User_model())->getUserDetails($userId) === null) {
            throw PageNotFoundException::forPageNotFound('User does not exist.');
        }

        $post = (array) $this->request->getPost();
        [$user, $permissions, $series, $error] = $this->validatedUser($post, $model, $userId, false);
        if ($error !== null) {
            return redirect()->to(site_url('admin/userDetails/' . $userId))->withInput()->with('error', $error);
        }

        if ($model->saveUser($userId, $user, $permissions, $series) === false) {
            throw new \RuntimeException('Unable to update user and save permissions.');
        }

        return redirect()->to(site_url('admin/userDetails/' . $userId));
    }

    public function updateFromPost(): RedirectResponse
    {
        $post = (array) $this->request->getPost();
        $userId = $this->postedId($post['targetUserId'] ?? null);
        if ($userId === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->update((string) $userId);
    }

    public function updatePassword(string $id = ''): RedirectResponse
    {
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $userId = (int) session()->get('userID');
        if ($id !== '' && (! ctype_digit($id) || (int) $id !== $userId)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $post = (array) $this->request->getPost();
        $oldPassword = $this->md5Hash($post['oldPassword'] ?? null);
        $password = $this->md5Hash($post['password1'] ?? null);
        $confirmation = $this->md5Hash($post['password2'] ?? null);
        if ($oldPassword === null || $password === null || $confirmation === null || $password !== $confirmation) {
            return redirect()->to(site_url('admin/changePassword/' . (int) session()->get('userID')))
                ->with('error', 'Enter valid password values and make sure the new passwords match.');
        }

        $model = new AdminUserManagementModel();
        if ($model->getPassword($userId) !== $oldPassword) {
            return redirect()->to(site_url('admin/changePassword/' . $userId))
                ->with('error', 'The current password is incorrect.');
        }
        if (! $model->setPassword($userId, $password)) {
            throw new \RuntimeException('Unable to change password.');
        }

        return redirect()->to(site_url('admin'))->with('message', 'Password changed successfully.');
    }

    public function updatePasswordOther(string $routeId = ''): RedirectResponse
    {
        $permission = $this->requirePermission('Create User');
        if ($permission instanceof RedirectResponse) {
            return $permission;
        }

        $post = (array) $this->request->getPost();
        $userId = $routeId === '' ? $this->postedId($post['id'] ?? null) : $this->postedId($routeId);
        if ($routeId !== '' && $this->postedId($post['id'] ?? null) !== $userId) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($userId === null || (new User_model())->getUserDetails($userId) === null) {
            throw PageNotFoundException::forPageNotFound('User does not exist.');
        }
        $password = $this->md5Hash($post['password1'] ?? null);
        $confirmation = $this->md5Hash($post['password2'] ?? null);
        if ($password === null || $confirmation === null || $password !== $confirmation) {
            return redirect()->to(site_url('admin/setPassword/' . $userId))
                ->with('error', 'Enter a valid password and make sure the passwords match.');
        }
        if (! (new AdminUserManagementModel())->setPassword($userId, $password)) {
            throw new \RuntimeException('Unable to set user password.');
        }

        return redirect()->to(site_url('admin/userDetails/' . $userId))->with('message', 'Password set successfully.');
    }

    private function validatedUser(array $post, AdminUserManagementModel $model, ?int $userId, bool $newUser): array
    {
        $username = $this->postString($post, 'username');
        $firstName = $this->postString($post, 'firstname');
        $lastName = $this->postString($post, 'lastname');
        $user = [
            'user_name' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
        if ($username === '' || mb_strlen($username) > 60 || $model->usernameExists($username, $userId)) {
            return [[], [], [], 'Choose a unique username of no more than 60 characters.'];
        }
        if ($firstName === '' || mb_strlen($firstName) > 30 || $lastName === '' || mb_strlen($lastName) > 30) {
            return [[], [], [], 'First and last names are required and must be no more than 30 characters.'];
        }

        if ($newUser) {
            $password = $this->md5Hash($post['password1'] ?? null);
            $confirmation = $this->md5Hash($post['password2'] ?? null);
            if ($password === null || $confirmation === null || $password !== $confirmation) {
                return [[], [], [], 'Enter a valid password and make sure the passwords match.'];
            }
            $user['password_enc'] = $password;
            $user['enctype'] = 'md5';
        }

        $permissionIds = array_fill_keys(array_map(
            static fn (array $permission): int => (int) $permission['permission_id'],
            $model->getPermissions()
        ), true);
        $seriesIds = array_fill_keys(array_map(
            static fn (array $series): int => (int) $series['id'],
            $model->getSeries()
        ), true);
        $permissions = $this->postedSelections($post, 'perm', $permissionIds);
        $series = $this->postedSelections($post, 'series', $seriesIds);
        if ($permissions === null || $series === null) {
            return [[], [], [], 'Permission selections were invalid. Reload the form and try again.'];
        }

        return [$user, $permissions, $series, null];
    }

    private function postedSelections(array $post, string $prefix, array $validIds): ?array
    {
        $selections = [];
        foreach ($validIds as $id => $_) {
            $key = $prefix . $id;
            $value = $post[$key] ?? '0';
            if (! is_scalar($value) || ! in_array((string) $value, ['0', '1'], true)) {
                return null;
            }
            $selections[$id] = (int) $value;
        }
        foreach ($post as $key => $_) {
            if (str_starts_with((string) $key, $prefix)
                && ! ctype_digit(substr((string) $key, strlen($prefix)))) {
                return null;
            }
            if (str_starts_with((string) $key, $prefix)) {
                $id = substr((string) $key, strlen($prefix));
                if (! ctype_digit($id) || ! isset($validIds[(int) $id])) {
                    return null;
                }
            }
        }

        return $selections;
    }

    private function requirePermission(string $permission): ?RedirectResponse
    {
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        if (($permissions[$permission] ?? 0) != 1) {
            return redirect()->to(site_url('admin'));
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

    private function postedId(mixed $value): ?int
    {
        return is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0 ? (int) $value : null;
    }

    private function postString(array $post, string $field): string
    {
        return is_scalar($post[$field] ?? null) ? trim((string) $post[$field]) : '';
    }

    private function md5Hash(mixed $value): ?string
    {
        return is_string($value) && preg_match('/^[a-f0-9]{32}$/i', $value) ? strtolower($value) : null;
    }
}
