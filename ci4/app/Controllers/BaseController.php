<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\LegacyInput;
use App\Libraries\LegacyLoader;
use App\Libraries\LegacySession;
use App\Libraries\LegacyUri;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'legacy_compat'];

    public LegacyInput $input;
    public LegacyLoader $load;
    public LegacySession $session;
    public LegacyUri $uri;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = new LegacySession(service('session'));
        $this->input = new LegacyInput($request);
        $this->uri = new LegacyUri($request);
        $this->load = new LegacyLoader($this, $this->session);
    }
}
