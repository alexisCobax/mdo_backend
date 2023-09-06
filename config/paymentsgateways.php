<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payments Gateways
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for all gateways payments
    |
    */

    'payeezy' => [
        'apiKey' => env('PAYEEZY_APIKEY'),
        'apiSecret' => env('PAYEEZY_APISECRET'),
        'merchantToken' => env('PAYEEZY_MERCHANTTOKEN'),
        'tokenUrl' => env('PAYEEZY_TOKENURL'),
        'url' => env('PAYEEZY_URL'),
    ],

    // 'clover' => [
    //     'token' => env('POSTMARK_TOKEN'),
    // ],

];
