<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class LangSwitch extends Controller
{
    public function switchLanguage(string $language = ''): \CodeIgniter\HTTP\ResponseInterface
    {
        $language = $language !== '' ? $language : 'english';
        service('session')->set('site_lang', $language);

        return $this->response->redirect(config('App')->baseURL);
    }
}
