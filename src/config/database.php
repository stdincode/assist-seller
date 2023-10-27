<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'pgsql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver'                  => 'sqlite',
            'url'                     => env('DATABASE_URL'),
            'database'                => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'                  => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver'         => 'mysql',
            'url'            => env('DATABASE_URL'),
            'host'           => env('DB_HOST', '127.0.0.1'),
            'port'           => env('DB_PORT', '3306'),
            'database'       => env('DB_DATABASE', 'forge'),
            'username'       => env('DB_USERNAME', 'forge'),
            'password'       => env('DB_PASSWORD', ''),
            'unix_socket'    => env('DB_SOCKET', ''),
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => true,
            'engine'         => null,
            'options'        => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver'         => 'pgsql',
            'host'           => env('POSTGRES_HOST', '127.0.0.1'),
            'port'           => env('POSTGRES_PORT', '5432'),
            'database'       => env('POSTGRES_DB', 'forge'),
            'username'       => env('POSTGRES_USER', 'forge'),
            'password'       => env('POSTGRES_PASSWORD', ''),
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
            'schema'         => 'public',
            'sslmode'        => 'prefer',
        ],

        'sqlsrv' => [
            'driver'         => 'sqlsrv',
            'url'            => env('DATABASE_URL'),
            'host'           => env('DB_HOST', 'localhost'),
            'port'           => env('DB_PORT', '1433'),
            'database'       => env('DB_DATABASE', 'forge'),
            'username'       => env('DB_USERNAME', 'forge'),
            'password'       => env('DB_PASSWORD', ''),
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
        ],

        'neo4j' => [
            'url'            => env('NEO4J_CONNECTION_URL'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix'  => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],

        'default' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

    'table_names' => [
        'users' => env('USERS_TABLE_NAME', 'users'),
        'places' => env('PLACES_TABLE_NAME', 'places'),
        'specializations' => env('SPECIALIZATIONS_TABLE_NAME', 'specializations'),
        'consultations' => env('CONSULTATIONS_TABLE_NAME', 'consultations'),
        'consultation_statuses' => env('CONSULTATION_STATUSES_TABLE_NAME', 'consultation_statuses'),
        'consultation_requests' => env('CONSULTATION_REQUESTS_TABLE_NAME', 'consultation_requests'),
        'consultation_request_statuses' => env('CONSULTATION_REQUEST_STATUSES_TABLE_NAME', 'consultation_request_statuses'),
        'experts' => env('EXPERTS_TABLE_NAME', 'experts'),
        'expert_payments' => env('EXPERT_PAYMENTS_TABLE_NAME', 'expert_payments'),
        'expert_payment_statuses' => env('EXPERT_PAYMENT_STATUSES_TABLE_NAME', 'expert_payment_statuses'),
        'expert_places' => env('EXPERT_PLACES_TABLE_NAME', 'expert_places'),
        'expert_specializations' => env('EXPERT_SPECIALIZATIONS_TABLE_NAME', 'expert_specializations'),
        'students' => env('STUDENTS_TABLE_NAME', 'students'),
        'expert_consultation_requests' => env('EXPERT_CONSULTATION_REQUESTS_TABLE_NAME', 'expert_consultation_requests'),
        'expert_consultation_request_statuses' => env('EXPERT_CONSULTATION_REQUEST_STATUSES_TABLE_NAME', 'expert_consultation_request_statuses'),
        'telegram_menus' => env('TELEGRAM_MENUS_TABLE_NAME', 'telegram_menus'),
        'telegram_menu_versions' => env('TELEGRAM_MENU_VERSIONS_TABLE_NAME', 'telegram_menu_versions'),
        'telegram_menu_sessions' => env('TELEGRAM_MENU_SESSIONS_TABLE_NAME', 'telegram_menu_sessions'),
        'telegram_client_steps' => env('TELEGRAM_CLIENT_STEPS_TABLE_NAME', 'telegram_client_steps'),
        'telegram_client_step_messages' => env('TELEGRAM_CLIENT_STEP_MESSAGES_TABLE_NAME', 'telegram_client_step_messages'),
        'telegram_clients' => env('TELEGRAM_CLIENTS_TABLE_NAME', 'telegram_clients'),
    ],

];
