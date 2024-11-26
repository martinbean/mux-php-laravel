<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mux access token
    |--------------------------------------------------------------------------
    |
    | Access tokens are used to authenticate requests to the Mux API. You can
    | create your own access token by logging in to the Mux Dashboard, and
    | going to Settings > Access Tokens.
    |
    */

    'client_id' => env('MUX_CLIENT_ID'),
    'client_secret' => env('MUX_CLIENT_SECRET'),

    'webhook' => [
        'secret' => env('MUX_WEBHOOK_SECRET'),
    ],

];
