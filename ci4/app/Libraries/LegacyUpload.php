<?php

namespace App\Libraries;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\Files\UploadedFile;

class LegacyUpload
{
    private ?UploadedFile $file = null;
    private array $fileData = [];
    private string $error = '';

    public function __construct(private Controller $controller, private array $config)
    {
    }

    public function do_upload(string $field): bool
    {
        $this->file = service('request')->getFile($field);
        if ($this->file === null || ! $this->file->isValid()) {
            $this->error = $this->file?->getErrorString() ?? 'No upload was received.';
            return false;
        }

        $allowedTypes = $this->config['allowed_types'] ?? '*';
        $extension = strtolower($this->file->getExtension());
        if ($allowedTypes !== '*' && ! in_array($extension, explode('|', strtolower($allowedTypes)), true)) {
            $this->error = 'The uploaded file type is not allowed.';
            return false;
        }

        $directory = WRITEPATH . 'uploads';
        if (! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            throw new \RuntimeException('Unable to create the upload directory.');
        }

        $name = $this->file->getRandomName();
        $this->file->move($directory, $name);
        $this->fileData = [
            'file_name' => $name,
            'full_path' => $directory . DIRECTORY_SEPARATOR . $name,
            'file_type' => $this->file->getClientMimeType(),
            'file_size' => $this->file->getSize() / 1024,
            'is_image' => false,
        ];

        return true;
    }

    public function display_errors(): string
    {
        return esc($this->error);
    }

    public function data(): array
    {
        return $this->fileData;
    }
}
