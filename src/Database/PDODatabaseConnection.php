<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use PDO;
use PDOException;
use App\Exceptions\DatabaseConnectionException;
use App\Exceptions\dataBaseConfigKeysException;
use App\Helpers\Config;

class PDODatabaseConnection implements DatabaseConnectionInterface
{
    private $config;
    private $connection;
    public function __construct(array $config)
    {
        if (!Config::areValidConfigKeys($config))
            throw new dataBaseConfigKeysException("Config keys are not valid!");
        $this->config = $config;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function generateDSN(array $config)
    {
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['dbname']}";
        return [$dsn, $config['username'], $config['password']];
    }

    public function connect()
    {
        $dsn = $this->generateDSN($this->config);
        try {
            $this->connection = new PDO(...$dsn);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException($e->getMessage() . " || " . $e->getLine() . " || " . $e->getFile());
        }
        return $this;
    }
}
