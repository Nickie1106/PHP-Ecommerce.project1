<?php 
define('BASE_PATH', '/');

try {
    $servername = "localhost";
    $username = "root";
    $password = "nishimura";
    $dbname = "php_project"; 

    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    echo "Connection successful to database: $dbname";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}

?>
