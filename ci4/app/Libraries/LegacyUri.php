<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;

class LegacyUri
{
    public function __construct(private CLIRequest|IncomingRequest $request)
    {
    }

    public function segment(int $number, mixed $default = null): mixed
    {
        $uri = $this->request->getUri();
        if ($number > count($uri->getSegments())) {
            return $default;
        }

        return $uri->getSegment($number);
    }
}
