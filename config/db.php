<?php
/**
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vice_city_services');
define('DB_PORT', 3306);
define('BASE_URL', '');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT); 

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

date_default_timezone_set('Asia/Kolkata');

?>
*/

<?php
$host = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: getenv('MYSQL_USER') ?: 'root';
$password = getenv('DB_PASS') ?: getenv('MYSQL_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: 'vice_city_services';

$port_env = getenv('DB_PORT') ?: getenv('MYSQL_PORT') ?: '3306';
$port = (int) $port_env;

ini_set('mysqli.connect_timeout', 5);
ini_set('default_socket_timeout', 5);

$conn = @new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed: " . htmlspecialchars($conn->connect_error));
}

$conn->set_charset('utf8mb4');
?>

