<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Synevo Website Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | Synevo Website Authentication username and password.
    | Used for requests to API.
    |
    */

    'username' => env('SITE_USERNAME', ''),
    'password' => env('SITE_PASSWORD', ''),

    'credentials' => env('SITE_USERNAME', '') . ':' . env('SITE_PASSWORD', ''),

];
