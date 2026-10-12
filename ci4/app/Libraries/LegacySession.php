<?php

namespace App\Libraries;

use CodeIgniter\Session\Session;

class LegacySession
{
    public function __construct(private Session $session)
    {
    }

    public function __get(string $name): mixed
    {
        if ($name === 'userdata') {
            return $this->session->get();
        }

        return null;
    }

    public function userdata(?string $key = null): mixed
    {
        return $key === null ? $this->session->get() : $this->session->get($key);
    }

    public function set_userdata(array|string $key, mixed $value = null): void
    {
        $this->session->set($key, $value);
    }

    public function unset_userdata(array|string $key): void
    {
        $this->session->remove($key);
    }

    public function sess_destroy(): void
    {
        $this->session->destroy();
    }
}
