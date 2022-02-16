<?php

namespace App\Helpers;

use Prophecy\Argument\Token\InArrayToken;

class Database
{
    const VALID_COLUMNS = [
        'user',
        'name',
        'link',
        'email'
    ];
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

    public static function updateColumnsForSqlStatement(array $data): string
    {
        $columnsAndValue = [];
        foreach ($data as $key => $value) {
            $columnsAndValue[] = "{$key} = ?";
        }
        $setSection = implode(',', $columnsAndValue);
        return $setSection;
    }

    public static function isValidColumnForWhereStatement(string $column): bool
    {
        return (in_array($column, self::VALID_COLUMNS));
    }
}
