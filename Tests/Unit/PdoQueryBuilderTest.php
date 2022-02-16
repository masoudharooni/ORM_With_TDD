<?php

namespace Test\Unit;

use App\Database\PDODatabaseConnection;
use PHPUnit\Framework\TestCase;
use App\Database\PdoQueryBuilder;
use App\Helpers\Config;
use App\Exceptions\ColumnDatabaseNotExistException;

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
            'name' => "First bug after update",
            'link' => "http://link.comAfterUpdate",
            'user' => "Masoud Haroon Updated",
            'email' => "masoudharooni50@gmail.comUUUUUUUPdated",
        ];
        $result = $queryBuilder->table('bugs')->where('user', 'Masoud Harooni')->update($data);
        $this->assertIsBool($result);
        return $queryBuilder;
    }
    /**
     * @depends testItCanUpdateData
     */
    public function testItShouldTrowsExceptionWhenColumnsNotExist($queryBuilder)
    {
        $this->expectException(ColumnDatabaseNotExistException::class);
        $result = $queryBuilder->table('bugs')->where('dummy', 'Masoud Harooni');
        $this->assertIsBool($result);
    }
    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
}
