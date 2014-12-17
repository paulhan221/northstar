<?php

return array(

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

  'connections' => array(

    'mysql' => array(
      'driver'    => 'mysql',
      'host'      => 'localhost',
      'database'  => 'homestead',
      'username'  => 'homestead',
      'password'  => 'secret',
      'charset'   => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix'    => '',
    ),

    'pgsql' => array(
      'driver'   => 'pgsql',
      'host'     => 'localhost',
      'database' => 'homestead',
      'username' => 'homestead',
      'password' => 'secret',
      'charset'  => 'utf8',
      'prefix'   => '',
      'schema'   => 'public',
    ),

    'mongodb' => array(
      'driver'   => 'mongodb',
      'host'     => $_ENV['DB_HOST'] ? $_ENV['DB_HOST'] : 'localhost',
      'port'     => $_ENV['DB_PORT'] ? $_ENV['DB_PORT'] : 27017,
      'username' => $_ENV['DB_USERNAME'] ? $_ENV['DB_USERNAME'] : '',
      'password' => $_ENV['DB_PASSWORD'] ? $_ENV['DB_PASSWORD'] : '',
      'database' => $_ENV['DB_NAME'] ? $_ENV['DB_NAME'] : 'userapi',
    ),

  ),

);
