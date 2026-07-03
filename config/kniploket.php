<?php

return [
    'per_page' => 4,
    'flash_auto_hide_ms' => 3000,

    /*
    |--------------------------------------------------------------------------
    | Lege database modus
    |--------------------------------------------------------------------------
    |
    | DB_USE_EMPTY=true  → kniploket_tiko_empty (tabellen, geen testdata)
    | DB_USE_EMPTY=false → kniploket_tiko (met testdata)
    |
    */
    'use_empty_database' => filter_var(env('DB_USE_EMPTY', false), FILTER_VALIDATE_BOOLEAN),
    'database_name' => filter_var(env('DB_USE_EMPTY', false), FILTER_VALIDATE_BOOLEAN)
        ? env('DB_DATABASE_EMPTY', 'kniploket_tiko_empty')
        : env('DB_DATABASE', 'kniploket_tiko'),
];
