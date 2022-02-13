<?php

namespace Tests\Unit;

use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Database\PDODatabaseConnection;
use App\Contracts\DatabaseConnectionInterface;
use App\Exceptions\DatabaseConnectionException;
use PDO;

class PDODatabaseConnectionTest extends TestCase
{
    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
    public function testItInstansOfDatabaseConnectionInterface()
    {
        $config = $this->getConfigs();
        $pdoConnection = new PDODatabaseConnection($config);
        $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdoConnection);
    }

    public function testConnetionMethodThatInstanceOfPDO()
    {
        $config = $this->getConfigs();
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoHandler = $pdoConnection->connect();
        $this->assertInstanceOf(PDO::class, $pdoConnection->getConnection());
        return $pdoHandler;
    }
    /**
     * @depends testConnetionMethodThatInstanceOfPDO
     */
    public function testConnectMethodShouldReturnValidInstanceOfPDODatabaseConnection($pdoHandler)
    {
        $this->assertInstanceOf(PDODatabaseConnection::class, $pdoHandler);
    }

    public function testItThrowsExceptionIfConfigIsNotValid()
    {
        $this->expectException(DatabaseConnectionException::class);
        $config = $this->getConfigs();
        $config['dbname'] = 'dummy';
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoConnection->connect();
    }
}
