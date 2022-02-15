<?php

namespace App\Helpers;

class Database
{
    public static function createPlaceholderForSqlStatement(array $data): string
    {
        $questionSignArray = [];
        foreach ($data as $value)
            $questionSignArray[] = '?';
        $placeholder = implode(',', $questionSignArray);
        return $placeholder;
    }

    public static function createColumnsListForSqlStatement(array $data): string
    {
        $arrayKeys = array_keys($data);
        $columns = implode(',', $arrayKeys);
        return $columns;
    }
}
