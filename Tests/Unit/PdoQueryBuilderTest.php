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

    public function testItCanUpdateData()
    {
        $config = $this->getConfigs();
        $dbInstance = new PDODatabaseConnection($config);
        $queryBuilder = new PdoQueryBuilder($dbInstance->connect());
        $data = [
            'name' => "First bug report222",
            'link' => "http://link.com2222",
            'user' => "Masoud Harooni22222",
            'email' => "masoudharooni50@gmail.com2222",
        ];
        $column_id = 23;
        $result = $queryBuilder->table('bugs')->update($data, $column_id);
        $this->assertIsBool($result);
    }

    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
}
