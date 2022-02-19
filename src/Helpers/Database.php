<?php

namespace App\Helpers;

class Database
{
    const VALID_COLUMNS = [
        'user',
        'name',
        'link',
        'email'
    ];
    const ALL_COLUMNS = [
        'id',
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

    public static function isValidColumn(string $column): bool
    {
        return (in_array($column, self::VALID_COLUMNS));
    }

    public static function isExistColumn(string $column): bool
    {
        return in_array($column, self::ALL_COLUMNS);
    }

    public static function implodeByAnd(array $argument): string
    {
        return implode(' AND ', $argument);
    }

    public static function implodeByComma(array $argument): string
    {
        return implode(',', $argument);
    }
}
