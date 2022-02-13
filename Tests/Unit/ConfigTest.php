<?php

namespace Test\Unit;

use App\Exceptions\dataBaseConfigFileNotExistException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    # Config::getFileContent testing 
    public function testGetFileContentMethodIsArray()
    {
        $config = Config::getFileContent('database');
        $this->assertIsArray($config);
    }
    public function testGetFileContentMethodTrowsExceptionWhenFileNotExist()
    {
        $this->expectException(dataBaseConfigFileNotExistException::class);
        Config::getFileContent('aFileThatNotExist');
    }
    # Config::get testing
    public function testGetMethodExpectedArray()
    {
        $config = Config::get('database', 'pdo');
        $expectedArray = [
            'driver' => 'mysql',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'orm',
            'charset'  => 'UTF-8',
            'host'     => 'localhost'
        ];
        $this->assertEquals($expectedArray, $config);
    }
    public function testGetMethodIsNullWhenKeyNotExist()
    {
        $config = Config::get('database', 'aKeyThatNotExist');
        $this->assertNull($config);
    }
    public function testGetMethodThrowsExceptionWhenFileNotExist()
    {
        $this->expectException(dataBaseConfigFileNotExistException::class);
        Config::get('aFileThatNotExist', 'aKeyThatNotExist');
    }
}
