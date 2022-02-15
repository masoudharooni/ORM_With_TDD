<?php

namespace App\Helpers;

use App\Exceptions\dataBaseConfigFileNotExistException;

class Config
{
    const VALID_CONFIG_KEYS = [
        'driver',
        'username',
        'password',
        'dbname',
        'charset',
        'host'
    ];
    public static function getFileContent(string $filename)
    {
        $path = realpath(__DIR__ . "/../Configs/{$filename}.php");
        if (!$path)
            throw new dataBaseConfigFileNotExistException();
        $result = require $path;
        return $result;
    }
    public static function get(string $filename, string $key)
    {
        $result = self::getFileContent($filename);
        return $result[$key] ?? null;
    }

    public static function areValidConfigKeys(array $config): bool
    {
        return (array_keys($config) == self::VALID_CONFIG_KEYS);
    }
}
