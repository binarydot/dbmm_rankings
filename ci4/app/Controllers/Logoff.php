<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Logoff extends BaseController
{
    public function index(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }
}
