<?php

namespace App\Libraries;

class LegacyLanguage
{
    public function line(string $key): string
    {
        return legacy_lang($key);
    }
}
