<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Daily Request Limit (per user, role = 'user')
    |--------------------------------------------------------------------------
    | Number of authenticated API requests a 'user'-role account may make in
    | a calendar day (UTC) before receiving HTTP 429. Admins are always
    | unlimited. Individual users can override this via the
    | user_rate_limit_overrides table.
    */
    'default_daily_user_limit' => (int) env('RATE_LIMIT_USER_DAILY', 1000),

    /*
    |--------------------------------------------------------------------------
    | Registration Daily Limit (per IP address)
    |--------------------------------------------------------------------------
    | Assumption: no specific number was provided by the product owner.
    | Defaulting to 1000 registrations/day/IP as a sane anti-abuse ceiling.
    | Adjust via RATE_LIMIT_REGISTER_IP_DAILY if a different figure is needed.
    */
    'register_ip_daily_limit' => (int) env('RATE_LIMIT_REGISTER_IP_DAILY', 1000),
];
