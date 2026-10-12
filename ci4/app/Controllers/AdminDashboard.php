<?php

namespace App\Controllers;

class AdminDashboard extends BaseController
{
    public function index(): string
    {
        if (! session()->get('loggedIn')) {
            return view('login/index', [
                'title' => 'Login',
                'menuItem' => 'login',
                'loginMessage' => 'Please enter your login details',
            ]);
        }

        $userId = (int) session()->get('userID');
        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $this->load->model('User_model');

        return view('admin/dashboard', [
            'title' => 'Admin',
            'menuItem' => 'admin',
            'firstName' => (string) session()->get('firstName'),
            'userId' => $userId,
            'permissions' => $permissions,
            'seriesPermissions' => $this->User_model->getSeriesPermissions($userId),
        ]);
    }
}
