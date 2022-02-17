<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Helpers\Database;
use App\Exceptions\ColumnDatabaseNotExistException;
use App\Exceptions\TableNotExistException;

class PdoQueryBuilder
{
    private $table;
    private $connection;
    private $whereSqlStatementCondition = [];
    public function __construct(DatabaseConnectionInterface $connection)
    {
        $this->connection = $connection->getConnection();
    }
    public function table(string $table_name)
    {
        if (!Database::isExistTable($table_name))
            throw new TableNotExistException("Table Not Exist!");
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

    public function where(string $column, $value)
    {
        if (!Database::isValidColumnForWhereStatement($column))
            throw new ColumnDatabaseNotExistException("Column is not exist!");
        $this->whereSqlStatementCondition[] = "{$column} = '{$value}'";
        return $this;
    }

    public function update(array $data): bool
    {
        $setSection = Database::updateColumnsForSqlStatement($data);
        $where = implode(' AND ', $this->whereSqlStatementCondition);
        $sql = "UPDATE {$this->table} SET {$setSection} WHERE {$where}";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array_values($data));
    }
}
