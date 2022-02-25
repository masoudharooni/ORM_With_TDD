<?php

namespace Test\Functional;

use PHPUnit\Framework\TestCase;
use App\Helpers\Config;
use App\Database\PdoQueryBuilder;
use App\Database\PDODatabaseConnection;
use App\Helpers\HttpClient;

class CrudTest extends TestCase
{
    private $HttpClient;
    private $queryBuilder;
    public function setUp(): void
    {
        $config = $this->getConfigs();
        $dbInstance = new PDODatabaseConnection($config);
        $this->queryBuilder = new PdoQueryBuilder($dbInstance->connect());
        $this->HttpClient = new HttpClient();
        parent::setUp();
    }

    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
}
