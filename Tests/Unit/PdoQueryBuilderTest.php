<?php

namespace Test\Unit;

use App\Database\PDODatabaseConnection;
use PHPUnit\Framework\TestCase;
use App\Database\PdoQueryBuilder;
use App\Helpers\Config;

class PdoQueryBuilderTest extends TestCase
{
    public function testItCanCreateData()
    {
        $config = $this->getConfigs();
        $dbInstance = new PDODatabaseConnection($config);
        $queryBuilder = new PdoQueryBuilder($dbInstance->connect());
        $data = [
            'name' => "First bug report",
            'link' => "http://link.com",
            'user' => "Masoud Harooni",
            'email' => "masoudharooni50@gmail.com",
        ];
        $result = $queryBuilder->table('bugs')->create($data);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
}
