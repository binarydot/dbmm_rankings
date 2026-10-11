<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class LegacyDatabase
{
    private array $where = [];

    public function __construct(private BaseConnection $connection)
    {
    }

    public function query(string $sql, array $binds = []): LegacyQueryResult
    {
        return new LegacyQueryResult($this->connection->query($sql, $binds));
    }

    public function insert(string $table, array $data): bool
    {
        return $this->connection->table($table)->insert($data);
    }

    public function insert_string(string $table, array $data): bool
    {
        return $this->insert($table, $data);
    }

    public function update(string $table, array $data, array $where = []): bool
    {
        $builder = $this->builderWithWhere($table, $where);
        $result = $builder->update($data);
        $this->where = [];
        return $result;
    }

    public function update_string(string $table, array $data): bool
    {
        return $this->update($table, $data);
    }

    public function delete(string $table, array $where = []): bool
    {
        $result = $this->builderWithWhere($table, $where)->delete();
        $this->where = [];
        return $result;
    }

    public function where(string|array $key, mixed $value = null): self
    {
        $this->where[] = is_array($key) ? $key : [$key => $value];
        return $this;
    }

    public function insert_id(): int|string
    {
        return $this->connection->insertID();
    }

    public function affected_rows(): int
    {
        return $this->connection->affectedRows();
    }

    public function last_query(): string
    {
        return (string) $this->connection->getLastQuery();
    }

    public function error(): array
    {
        return $this->connection->error();
    }

    public function __get(string $name): mixed
    {
        if ($name === 'affected_rows') {
            return $this->connection->affectedRows();
        }

        throw new \RuntimeException("Unsupported legacy database property: {$name}");
    }

    private function builderWithWhere(string $table, array $where = [])
    {
        $builder = $this->connection->table($table);
        foreach ($this->where as $condition) {
            $builder->where($condition);
        }
        if ($where !== []) {
            $builder->where($where);
        }

        return $builder;
    }
}
