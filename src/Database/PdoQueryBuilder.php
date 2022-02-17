<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Helpers\Database;
use App\Exceptions\ColumnDatabaseNotExistException;
use App\Exceptions\TableNotExistException;
use App\Exceptions\FieldIsNotExistException;
use PDO;

class PdoQueryBuilder
{
    private $table;
    private $connection;
    private $whereSqlStatementCondition = [];
    private $fieldsForGetMethod = [];
    private $paginationParams = [];
    public function __construct(DatabaseConnectionInterface $connection)
    {
        $this->connection = $connection->getConnection();
    }
    public function table(string $table_name)
    {
        if (!$this->isExistTable($table_name))
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
        if (!Database::isValidColumn($column))
            throw new ColumnDatabaseNotExistException("Column is not exist!");
        $this->whereSqlStatementCondition[] = "{$column} = '{$value}'";
        return $this;
    }

    public function update(array $data): int
    {
        $setSection = Database::updateColumnsForSqlStatement($data);
        $where = implode(' AND ', $this->whereSqlStatementCondition);
        $sql = "UPDATE {$this->table} SET {$setSection} WHERE {$where}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
        return $stmt->rowCount();
    }

    private function isExistTable(string $table_name)
    {
        $tables = $this->getAllTables();
        return (in_array($table_name, $tables));
    }

    public function delete(): int
    {
        $where = implode(' AND ', $this->whereSqlStatementCondition);
        $sql = "DELETE FROM {$this->table} WHERE {$where};";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function get(): ?array
    {
        $where = !count($this->whereSqlStatementCondition) ? null
            : " WHERE " . implode(' AND ', $this->whereSqlStatementCondition);
        $fields = !count($this->fieldsForGetMethod) ? "*"
            : implode(',', $this->fieldsForGetMethod);
        $pagination = !count($this->paginationParams) ? null
            : " LIMIT " . implode(',', $this->paginationParams);
        $sql = "SELECT {$fields} FROM {$this->table} {$where} {$pagination}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return (count($result) > 0 ? $result : null);
    }

    public function field(array $fields = [])
    {
        foreach ($fields as $field)
            if (!Database::isValidColumn($field))
                throw new FieldIsNotExistException("Field is not exist!");
        $this->fieldsForGetMethod = $fields;
        return $this;
    }

    public function pagination(int $page = null, int  $page_size = null)
    {
        if (!is_null($page) and !is_null($page_size)) {
            $startPoint = ($page - 1) * $page_size;
            $this->paginationParams = ['start_point' => $startPoint, 'page_size' => $page_size];
        }
        return $this;
    }

    private function getAllTables()
    {
        $query = $this->connection->prepare("SHOW TABLES");
        $query->execute();
        $tables = $query->fetchAll(PDO::FETCH_COLUMN);
        return $tables;
    }

    public function truncateAllTables()
    {
        $tables = $this->getAllTables();
        foreach ($tables as $table) {
            $query = $this->connection->prepare("TRUNCATE TABLE {$table}");
            $query->execute();
        }
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }
    public function rollback()
    {
        $this->connection->rollback();
    }
}
