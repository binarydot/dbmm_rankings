<?php

namespace App\Controllers;

use App\Libraries\LegacyController as CI_Controller;

class LangSwitch extends CI_Controller
{
    public function switchLanguage(string $language = ''): \CodeIgniter\HTTP\RedirectResponse
    {
        $language = $language !== '' ? $language : 'english';
        $this->session->set_userdata('site_lang', $language);

        return redirect()->to(base_url());
    }
}
