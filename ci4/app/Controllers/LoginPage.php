<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class LoginPage extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('loggedIn')) {
            return redirect()->to(site_url('admin'));
        }

        return view('login/index', [
            'title' => 'Login',
            'menuItem' => 'login',
            'loginMessage' => session()->getFlashdata('error') ?? 'Please enter your login details',
        ]);
    }
}
