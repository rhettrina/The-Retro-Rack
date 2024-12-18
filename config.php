<?php


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'u143688490_wiz');
define('DB_PASSWORD', 'Louis@18');
define('DB_NAME', 'u143688490_retrorack');


$conn = mysqli_connect(
    DB_SERVER,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME
);

if ($conn == false)
    die('Error: Cannot connect');


