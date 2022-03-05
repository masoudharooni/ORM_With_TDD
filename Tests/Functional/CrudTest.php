<?php

namespace Tests\Functional;

use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Database\PDODatabaseConnection;
use App\database\PdoQueryBuilder;
use App\Helpers\HttpClient;

class CrudTest extends TestCase
{
    private $queryBuilder;
    private $HttpClient;
    public function setUp(): void
    {
        $config = $this->getConfigs();
        $dbInstance = new PDODatabaseConnection($config);
        $this->queryBuilder = new PdoQueryBuilder($dbInstance->connect());
        $this->HttpClient = new HttpClient();
        parent::setUp();
    }
    public function tearDown(): void
    {
        $this->HttpClient = null;
        parent::tearDown();
    }

    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
}
