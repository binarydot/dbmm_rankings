<?php

namespace App\Libraries;

use CodeIgniter\Controller;
use App\Libraries\LegacyModel;

class LegacyLoader
{
    public function __construct(
        private Controller $controller,
        private LegacySession $session
    ) {
    }

    public function model(string $name, ?string $alias = null): void
    {
        $class = '\\App\\Models\\' . $name;
        if (! class_exists($class)) {
            throw new \RuntimeException("CI4 model {$class} is missing.");
        }

        $model = new $class();
        if (! $model instanceof LegacyModel) {
            throw new \RuntimeException("CI4 model {$class} must extend LegacyModel.");
        }

        $this->controller->{$alias ?? $name} = $model;
    }

    public function view(string $name, ?array $data = null, bool $return = false): ?string
    {
        $viewData = $data ?? [];
        $viewData['legacySession'] = $this->session;
        $rendered = view('legacy/' . $name, $viewData);

        if ($return) {
            return $rendered;
        }

        echo $rendered;
        return null;
    }

    public function helper(string|array $helpers): void
    {
        helper($helpers);
    }

    public function library(string $name, array $config = []): void
    {
        if ($name !== 'upload') {
            throw new \RuntimeException("CI3 library {$name} has not been migrated.");
        }

        $this->controller->upload = new LegacyUpload($this->controller, $config);
    }
}
