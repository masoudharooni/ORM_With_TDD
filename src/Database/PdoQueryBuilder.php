<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Helpers\Database;

class PdoQueryBuilder
{
    private $table;
    private $connection;
    public function __construct(DatabaseConnectionInterface $connection)
    {
        $this->connection = $connection->getConnection();
    }
    public function table(string $table_name)
    {
        $this->table = $table_name;
        return $this;
    }

    public function create(array $data): int
    {
        $columns = Database::createColumnsListForSqlStatement($data);
        $placeholder = Database::createPlaceholderForSqlStatement($data);
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholder})";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
        return (int)$this->connection->lastInsertId();
    }

    public function update(array $data, int $column_id): bool
    {
        $columnsAndValue = [];
        foreach ($data as $key => $value) {
            $columnsAndValue[] = "{$key} = ?";
        }
        $setSection = implode(',', $columnsAndValue);
        $sql = "UPDATE {$this->table} SET {$setSection} WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        array_push($data, $column_id);
        return $stmt->execute(array_values($data));
    }
}
