<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NTLM Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | NTLM Authentication username and password.
    | Used for requests to "preordering" API.
    |
    */

    'username' => env('NTLM_USERNAME', ''),
    'password' => env('NTLM_PASSWORD', ''),

    'credentials' => env('NTLM_USERNAME', '') . ':' . env('NTLM_PASSWORD', ''),

];
