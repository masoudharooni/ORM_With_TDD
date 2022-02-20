<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Helpers\Database;
use App\Exceptions\ColumnDatabaseNotExistException;
use App\Exceptions\TableNotExistException;
use App\Exceptions\FieldIsNotExistException;
use App\Exceptions\sortMethodException;
use App\Exceptions\whereEmptyException;
use PDO;

class PdoQueryBuilder
{
    private $table;
    private $connection;
    private $whereSqlStatementCondition = [];
    private $fieldsForGetMethod = [];
    private $paginationParams = [];
    private $sortParams = [];
    private $values = [];
    private $statement;
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
        $this->values = array_values($data);
        $this->execute($sql);
        return (int)$this->connection->lastInsertId();
    }

    public function where(string $column, $value)
    {
        if (!Database::isValidColumn($column))
            throw new ColumnDatabaseNotExistException("Column is not exist!");
        $this->whereSqlStatementCondition[] = "{$column} = '{$value}'";
        return $this;
    }

    public function sort(string $sortBy, string $sortMethod = 'ASC')
    {
        if (!Database::isExistColumn($sortBy))
            throw new ColumnDatabaseNotExistException("Column is not exist!");
        if (!in_array($sortMethod, ['ASC', 'DESC', 'asc', 'desc']))
            throw new sortMethodException("it should be asc or desc!");

        $this->sortParams = ['sortBy' => $sortBy, 'sortMethod' => strtoupper($sortMethod)];
        return $this;
    }

    public function update(array $data): int
    {
        if (empty($this->whereSqlStatementCondition))
            throw new whereEmptyException("Where Statement is empty!");

        $setSection = Database::updateColumnsForSqlStatement($data);
        $where = Database::implodeByAnd($this->whereSqlStatementCondition);
        $sql = "UPDATE {$this->table} SET {$setSection} WHERE {$where}";
        $this->values = array_values($data);
        $this->execute($sql);
        return $this->statement->rowCount();
    }

    private function isExistTable(string $table_name)
    {
        $tables = $this->getAllTables();
        return (in_array($table_name, $tables));
    }

    public function delete(): int
    {
        if (empty($this->whereSqlStatementCondition))
            throw new whereEmptyException("Where Statement is empty!");

        $where = Database::implodeByAnd($this->whereSqlStatementCondition);
        $sql = "DELETE FROM {$this->table} WHERE {$where};";
        $this->execute($sql);
        return $this->statement->rowCount();
    }

    public function get(): ?array
    {
        $where = !count($this->whereSqlStatementCondition) ? null
            : " WHERE " . Database::implodeByAnd($this->whereSqlStatementCondition);
        $fields = !count($this->fieldsForGetMethod) ? "*"
            : Database::implodeByComma($this->fieldsForGetMethod);
        $pagination = !count($this->paginationParams) ? null
            : " LIMIT " . Database::implodeByComma($this->paginationParams);
        $orderBySection = !count($this->sortParams) ? null
            : "ORDER BY {$this->sortParams['sortBy']} {$this->sortParams['sortMethod']}";
        $sql = "SELECT {$fields} FROM {$this->table} {$where} {$orderBySection} {$pagination}";
        $this->execute($sql);
        $result = $this->statement->fetchAll();
        return (count($result) > 0 ? $result : null);
    }

    public function first()
    {
        $result = $this->get();
        return is_null($result) ? null : $result[0];
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
        $this->execute("SHOW TABLES");
        $tables = $this->statement->fetchAll(PDO::FETCH_COLUMN);
        return $tables;
    }

    public function truncateAllTables()
    {
        $tables = $this->getAllTables();
        foreach ($tables as $table)
            $this->execute("TRUNCATE TABLE {$table}");
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }
    public function rollback()
    {
        $this->connection->rollback();
    }
    private function execute(string $sql)
    {
        $this->statement =  $this->connection->prepare($sql);
        $this->statement->execute($this->values);
        $this->values = [];
        return $this;
    }
}
