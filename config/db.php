<?php
/**
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vice_city_services');
define('DB_PORT', 3306);
define('BASE_URL', '');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); */

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

date_default_timezone_set('Asia/Kolkata');

?>

