<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'enable_3ds' => (bool) env('MIDTRANS_ENABLE_3DS', true),
    'disable_ssl_verification' => (bool) env('MIDTRANS_DISABLE_SSL_VERIFICATION', false),
    'merchant_name' => env('MIDTRANS_MERCHANT_NAME', 'Mandala Bimbel'),
];
