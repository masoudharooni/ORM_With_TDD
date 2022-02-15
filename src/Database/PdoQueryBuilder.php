<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;

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
        $arrayKeys = array_keys($data);
        $columns = implode(',', $arrayKeys);
        $questionSignArray = [];
        foreach ($data as $value)
            $questionSignArray[] = '?';
        $placeholder = implode(',', $questionSignArray);
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholder})";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
        return (int)$this->connection->lastInsertId();
    }
}
