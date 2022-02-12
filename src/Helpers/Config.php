<?php

namespace App\Helpers;

use App\Exceptions\dataBaseConfigFileNotExistException;

class Config
{
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
}
