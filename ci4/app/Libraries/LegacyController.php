<?php

namespace App\Libraries;

use App\Controllers\BaseController;

abstract class LegacyController extends BaseController
{
    private array $legacyModels = [];
    private ?LegacyLanguage $legacyLanguage = null;

    public function __set(string $name, mixed $value): void
    {
        $this->legacyModels[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        if ($name === 'lang') {
            return $this->legacyLanguage ??= new LegacyLanguage();
        }

        if (array_key_exists($name, $this->legacyModels)) {
            return $this->legacyModels[$name];
        }

        throw new \RuntimeException("Legacy controller property {$name} has not been loaded.");
    }
}
