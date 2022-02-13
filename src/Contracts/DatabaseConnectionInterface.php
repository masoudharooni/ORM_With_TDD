<?php

namespace App\Contracts;

interface DatabaseConnectionInterface
{
    public function connection();
    public function getConnection();
}
