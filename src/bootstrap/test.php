<?php

use App\Database\PDODatabaseConnection;
use App\Database\PdoQueryBuilder;
use App\Helpers\Config;

$autoloaderPath = realpath(__DIR__ . "/../../vendor/autoload.php");
require_once $autoloaderPath;
$config = Config::get('database', 'pdo_testing');
$connection = new PDODatabaseConnection($config);
$queryBuilder = new PdoQueryBuilder($connection->connect());
$queryBuilder->truncateAllTables();
