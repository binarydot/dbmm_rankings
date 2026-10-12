<?php

namespace App\Controllers;

use App\Models\AuthenticationModel;
use CodeIgniter\HTTP\RedirectResponse;

class Login extends BaseController
{
    public function submit(): RedirectResponse
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (! is_string($username) || trim($username) === ''
            || ! is_string($password) || ! preg_match('/\A[a-fA-F0-9]{32}\z/', $password)) {
            $this->clearAuthentication();

            return redirect()->to(site_url('login'))
                ->withInput(['username' => is_string($username) ? $username : ''])
                ->with('error', 'Enter your username and password.');
        }

        $model = new AuthenticationModel();
        $user = $model->authenticate(trim($username), strtolower($password));
        if ($user === null) {
            $this->clearAuthentication();

            return redirect()->to(site_url('login'))
                ->withInput(['username' => $username])
                ->with('error', 'Username or password is incorrect.');
        }

        $session = session();
        $session->regenerate(true);
        $this->clearAuthentication();
        $session->set([
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'currentUser' => $user['user_name'],
            'userID' => (int) $user['user_id'],
            'loggedIn' => true,
            'permissions' => $model->getPermissions((int) $user['user_id']),
            'seriesPermissions' => $model->getSeriesPermissions((int) $user['user_id']),
        ]);

        return redirect()->to(site_url('admin'));
    }

    private function clearAuthentication(): void
    {
        session()->remove([
            'firstName',
            'lastName',
            'currentUser',
            'userID',
            'userLevel',
            'loggedIn',
            'permissions',
            'seriesPermissions',
        ]);
    }
}
