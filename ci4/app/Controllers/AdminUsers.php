<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class AdminUsers extends BaseController
{
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        if (! session()->get('loggedIn') || ($permissions['User Menu'] ?? 0) != 1) {
            return redirect()->to(site_url('admin'));
        }

        $this->load->model('User_model');

        return view('admin/users', [
            'title' => 'User List',
            'menuItem' => 'admin',
            'userId' => (int) session()->get('userID'),
            'permissions' => $permissions,
            'users' => $this->User_model->listUsers(),
        ]);
    }

    public function details(string $id = ''): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        if (! session()->get('loggedIn') || ($permissions['User Menu'] ?? 0) != 1) {
            return redirect()->to(site_url('admin'));
        }
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->load->model('User_model');
        $user = $this->User_model->getUserDetails((int) $id);
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('User does not exist.');
        }

        return view('admin/user-details', [
            'title' => 'User Details',
            'menuItem' => 'admin',
            'userId' => (int) session()->get('userID'),
            'permissions' => $permissions,
            'user' => $user,
            'userPermissions' => $this->User_model->getUserPermissionSettings((int) $id),
            'seriesPermissions' => $this->User_model->getUserSeriesSettings((int) $id),
        ]);
    }

    public function add(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        if (! session()->get('loggedIn') || ($permissions['Create User'] ?? 0) != 1) {
            return redirect()->to(site_url('admin'));
        }

        $this->load->model('User_model');

        return view('admin/add-user', [
            'title' => 'Add User',
            'menuItem' => 'admin',
            'userId' => (int) session()->get('userID'),
            'permissions' => $permissions,
            'userPermissions' => $this->User_model->getAllPermissions(),
            'seriesPermissions' => $this->User_model->getAllSeriesPermissions(),
        ]);
    }

    public function setPassword(string $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        if (! session()->get('loggedIn') || ($permissions['Create User'] ?? 0) != 1) {
            return redirect()->to(site_url('admin'));
        }
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->load->model('User_model');
        $user = $this->User_model->getUserDetails((int) $id);
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound('User does not exist.');
        }

        return view('admin/set-password', [
            'title' => "Set User's Password",
            'menuItem' => 'admin',
            'userId' => (int) session()->get('userID'),
            'permissions' => $permissions,
            'targetUser' => $user,
        ]);
    }

    public function changePassword(string $id = ''): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $currentUserId = (int) session()->get('userID');
        if ($id !== '' && (! ctype_digit($id) || (int) $id !== $currentUserId)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];

        return view('admin/change-password', [
            'title' => 'Change Password',
            'menuItem' => 'admin',
            'userId' => $currentUserId,
            'permissions' => $permissions,
        ]);
    }
}
