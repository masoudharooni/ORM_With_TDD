<?php

namespace Test\Unit;

use App\Database\PDODatabaseConnection;
use PHPUnit\Framework\TestCase;
use App\Database\PdoQueryBuilder;
use App\Helpers\Config;
use App\Exceptions\ColumnDatabaseNotExistException;
use App\Exceptions\TableNotExistException;
use App\Exceptions\FieldIsNotExistException;
use App\Exceptions\sortMethodException;
use App\Exceptions\whereEmptyException;

class PdoQueryBuilderTest extends TestCase
{
    private $queryBuilder;
    public function setUp(): void
    {
        $config = $this->getConfigs();
        $dbInstance = new PDODatabaseConnection($config);
        $this->queryBuilder = new PdoQueryBuilder($dbInstance->connect());
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }

    public function testItCanCreateData()
    {
        $result = $this->insertIntoDb();
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanUpdateData()
    {
        $this->insertIntoDb();
        $data = [
            'name' => "First bug after update",
            'link' => "http://link.comAfterUpdate",
            'user' => "Masoud Haroon Updated",
            'email' => "masoudharooni50@gmail.comUUUUUUUPdated",
        ];
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'Masoud Harooni')
            ->update($data);
        $this->assertEquals(1, $result);
        return $this->queryBuilder;
    }

    public function testItShouldTrowsExceptionWhenWhereStatementIsEmpty()
    {
        $this->expectException(whereEmptyException::class);
        $this->queryBuilder
            ->table('bugs')
            ->update(['user' => 'Masoud Harooni After Update']);
    }


    public function testItShouldReturnZeroWhenTheRowIsNotExist()
    {
        $this->insertIntoDb();
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'Dummy user')
            ->update(['name' => 'after Update']);
        $this->assertEquals(0, $result);
    }

    public function testMultipleWhere()
    {
        $this->insertIntoDb();
        $this->insertIntoDb(['user' => 'Ali Harooni', 'link' => 'forExample.com']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'Ali Harooni')
            ->where('link', 'forExample.com')
            ->update(['user' => 'hello this is user']);
        $this->assertEquals(1, $result);
    }


    /**
     * @depends testItCanUpdateData
     */
    public function testItShouldTrowsExceptionWhenColumnsNotExist($queryBuilder)
    {
        $this->expectException(ColumnDatabaseNotExistException::class);
        $queryBuilder->table('bugs')->where('dummy', 'Masoud Harooni');
    }

    public function testTableMethodShouldReturnAnInstanceOfPdoQueryBuilderClass()
    {
        $result = $this->queryBuilder->table('bugs');
        $this->assertInstanceOf(PdoQueryBuilder::class, $result);
    }

    public function testWhereMethodShouldReturnAnInstanceOfPdoQueryBUilderClass()
    {
        $result = $this->queryBuilder->table('bugs')->where('user', 'masoud');
        $this->assertInstanceOf(PdoQueryBuilder::class, $result);
    }

    public function testSortMethodShouldReturnAnInstanceOfPdoQueryBUilderClass()
    {
        $result = $this->queryBuilder->table('bugs')->sort('id');
        $this->assertInstanceOf(PdoQueryBuilder::class, $result);
    }

    public function testPaginationMethodShouldReturnAnInstanceOfPdoQueryBUilderClass()
    {
        $result = $this->queryBuilder->table('bugs')->pagination();
        $this->assertInstanceOf(PdoQueryBuilder::class, $result);
    }

    public function testTableMethodShouldThrowsExceptionWhenTableIsNotValid()
    {
        $this->expectException(TableNotExistException::class);
        $this->queryBuilder->table('dummy');
    }

    public function testItCanDeleteRecord()
    {
        $this->insertIntoDb();
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'Masoud Harooni')
            ->delete();
        $this->assertEquals(1, $result);
    }

    public function testDeleteRecordWithoutWhere()
    {
        $this->expectException(whereEmptyException::class);
        $this->queryBuilder
            ->table('bugs')
            ->delete();
    }

    public function testItCanFetchData()
    {
        $this->multipleInsertIntoDb(10, ['user' => 'Ali']);
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'Ali')
            ->get();
        $this->assertIsArray($result);
        $this->assertCount(10, $result);
    }

    public function testItCanFetchDataWithoutWhere()
    {
        $this->multipleInsertIntoDb(10, ['user' => 'Ali']);
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('bugs')
            ->get();
        $this->assertIsArray($result);
        $this->assertCount((10 + 10), $result);
    }

    public function testItCanFetchDataWithSorting()
    {
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('bugs')
            ->sort('id', 'ASC')
            ->get();
        $this->assertIsArray($result);
        $this->assertCount(10, $result);
        $this->assertObjectHasAttribute('user', $result[0]);
        $this->assertObjectHasAttribute('name', $result[0]);
        $this->assertObjectHasAttribute('link', $result[0]);
        $this->assertObjectHasAttribute('email', $result[0]);
    }

    public function testItShouldThrowsExceptionWhenSortByArgumentIsNotCorrect()
    {
        $this->expectException(ColumnDatabaseNotExistException::class);
        $this->queryBuilder
            ->table('bugs')
            ->sort('dummy', 'ASC');
    }

    public function testItShouldThrowsExceptionWhenSortMethodArgumentIsNotCorrect()
    {
        $this->expectException(sortMethodException::class);
        $this->queryBuilder
            ->table('bugs')
            ->sort('id', 'dummy');
    }

    public function testItCanReturnNullWhenAreNotExistAnyData()
    {
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'dummy')
            ->get();
        $this->assertNull($result);
    }

    public function testGettingSpecialColumnWithGetMethod()
    {
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('bugs')
            ->field(['user', 'link'])
            ->get();
        $this->assertIsArray($result);
        $this->assertCount(10, $result);
        $this->assertObjectHasAttribute('user', $result[0]);
        $this->assertObjectHasAttribute('link', $result[0]);
        $objectToArray = json_decode(json_encode($result), true);
        $this->assertEquals(['user', 'link'], array_keys($objectToArray[0]));
    }

    public function testItCatFetchFirstRow()
    {
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('bugs')
            ->first();
        $this->assertIsObject($result);
        $this->assertObjectHasAttribute('user', $result);
        $this->assertObjectHasAttribute('link', $result);
        $this->assertObjectHasAttribute('name', $result);
        $this->assertObjectHasAttribute('email', $result);
    }

    public function testItReturnNullIfFirstRowNotExist()
    {
        $result = $this->queryBuilder
            ->table('bugs')
            ->first();
        $this->assertNull($result);
    }

    public function testItShouldThrowsExceptionWhenFieldsAreNotValid()
    {
        $this->expectException(FieldIsNotExistException::class);
        $this->queryBuilder
            ->table('bugs')
            ->field(['dummy field']);
    }

    public function testItCanFetchDataWithPagination()
    {
        $this->multipleInsertIntoDb(10);
        $result = $this->queryBuilder
            ->table('bugs')
            ->pagination(2, 3)
            ->get();
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function tearDown(): void
    {
        // $this->queryBuilder->truncateAllTables();
        $this->queryBuilder->rollback();
        parent::tearDown();
    }

    private function insertIntoDb(array $option = [])
    {
        $data = array_merge([
            'name' => "First bug report",
            'link' => "http://link.com",
            'user' => "Masoud Harooni",
            'email' => "masoudharooni50@gmail.com",
        ], $option);
        $result = $this->queryBuilder->table('bugs')->create($data);
        return $result;
    }

    private function multipleInsertIntoDb(int $count, array $option = [])
    {
        for ($i = 0; $i < $count; $i++)
            $this->insertIntoDb($option);
    }

    private function getConfigs()
    {
        return Config::get('database', 'pdo_testing');
    }
}
