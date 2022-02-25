<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class HttpClient extends Client
{
    public function __construct()
    {
        parent::__construct(['base_uri' => 'http://localhost:8000']);
    }
}
