<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | API Authentication username and password.
    | Used for requests to this application's API.
    |
    | random_bytes used to avoid insecure situation, in cases
    | when API_USERNAME and/or API_PASSWORD does not exists
    | in .env or does not have any values. During these cases
    | application endpoints will be unreachable.
    |
    */

    'username' => env('API_USERNAME', random_bytes(32)),
    'password' => env('API_PASSWORD', random_bytes(32)),

    'credentials' => env('API_USERNAME', '') . ':' . env('API_PASSWORD', ''),

];
