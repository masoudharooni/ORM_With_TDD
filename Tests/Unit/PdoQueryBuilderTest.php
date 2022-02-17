<?php

namespace Test\Unit;

use App\Database\PDODatabaseConnection;
use PHPUnit\Framework\TestCase;
use App\Database\PdoQueryBuilder;
use App\Helpers\Config;
use App\Exceptions\ColumnDatabaseNotExistException;

class PdoQueryBuilderTest extends TestCase
{
    private $queryBuilder;
    public function setUp(): void
    {
        $config = $this->getConfigs();
        $dbInstance = new PDODatabaseConnection($config);
        $this->queryBuilder = new PdoQueryBuilder($dbInstance->connect());
        parent::setUp();
    }

    public function testItCanCreateData()
    {
        $data = [
            'name' => "First bug report",
            'link' => "http://link.com",
            'user' => "Masoud Harooni",
            'email' => "masoudharooni50@gmail.com",
        ];
        $result = $this->queryBuilder->table('bugs')->create($data);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanUpdateData()
    {
        $data = [
            'name' => "First bug after update",
            'link' => "http://link.comAfterUpdate",
            'user' => "Masoud Haroon Updated",
            'email' => "masoudharooni50@gmail.comUUUUUUUPdated",
        ];
        $result = $this->queryBuilder->table('bugs')->where('user', 'Masoud Harooni')->update($data);
        $this->assertIsBool($result);
        return $this->queryBuilder;
    }
    /**
     * @depends testItCanUpdateData
     */
    public function testItShouldTrowsExceptionWhenColumnsNotExist($queryBuilder)
    {
        $this->expectException(ColumnDatabaseNotExistException::class);
        $queryBuilder->table('bugs')->where('dummy', 'Masoud Harooni');
    }
    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
}
