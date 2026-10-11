<?php

namespace App\Libraries;

use CodeIgniter\Database\ResultInterface;

class LegacyQueryResult
{
    public function __construct(private ResultInterface $result)
    {
    }

    public function result(): array
    {
        return $this->result->getResult();
    }

    public function result_array(): array
    {
        return $this->result->getResultArray();
    }

    public function row(?int $index = 0): ?object
    {
        return $this->result->getRow($index);
    }

    public function row_array(?int $index = 0): ?array
    {
        return $this->result->getRowArray($index);
    }

    public function num_rows(): int
    {
        return $this->result->getNumRows();
    }

    public function __get(string $name): mixed
    {
        return $this->result->{$name};
    }
}
