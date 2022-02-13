<?php

namespace Tests\Unit;

use App\Helpers\Config;
use PHPUnit\Framework\TestCase;
use App\Database\PDODatabaseConnection;
use App\Contracts\DatabaseConnectionInterface;

class PDODatabaseConnectionTest extends TestCase
{
    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
    public function testItInstansOfDatabaseConnectionInterface()
    {
        $config = $this->getConfigs();
        $PDODatabaseConnection = new PDODatabaseConnection($config);
        $this->assertInstanceOf(DatabaseConnectionInterface::class, $PDODatabaseConnection);
    }
}
