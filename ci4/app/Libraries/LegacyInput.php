<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;

class LegacyInput
{
    public function __construct(private CLIRequest|IncomingRequest $request)
    {
    }

    public function post(?string $key = null, bool $xssClean = false): mixed
    {
        return $this->request->getPost($key);
    }

    public function get(?string $key = null, bool $xssClean = false): mixed
    {
        return $this->request->getGet($key);
    }

    public function get_post(?string $key = null, bool $xssClean = false): mixed
    {
        return $this->request->getPost($key) ?? $this->request->getGet($key);
    }
}
