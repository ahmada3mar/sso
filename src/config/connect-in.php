<?php

return [


    /*
    |--------------------------------------------------------------------------
    | ACI default response
    |--------------------------------------------------------------------------
    |
    | When request receved from ACI
    | the default_response returned to ACI to tell ACI
    | about transaction status and description
    | by the default we returned 000.200.00 that indicate the transaction is pendding
    |
    */

    'default_response' => [
        'aci_code' => '000.200.000',
        'description' => 'Transaction Successful',
    ],


    /*
    |--------------------------------------------------------------------------
    | ACI payment api end-point
    |--------------------------------------------------------------------------
    |
    | The end point that fire connect-in function
    | by default we use
    | 1- v1/connectin/payment for payment(create transaction)
    | 2- v1/connectin/payment/{transaction}  for handle
    |
    */

    'end_points' => [
        'payment' => 'v1/connectin/payment',
        'refund' => 'v1/connectin/payment/{transaction}',
    ],

];
