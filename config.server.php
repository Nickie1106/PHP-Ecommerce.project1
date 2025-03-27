<?php
define('BASE_PATH', '/project_php/');

try {
    $servername = "localhost";
    $username = "nishimura";
    $password = "nishimura";
    $dbname = "nishimura_php_project"; 

    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    echo "Connection successful to database: $dbname";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}

?>
