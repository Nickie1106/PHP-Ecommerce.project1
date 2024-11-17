<?php
// connection.php
$servername = "localhost"; 
$username = "nishimura"; 
$password = "nishimura"; 
$dbname = "nishimura_php_project"; 

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーのチェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

}
?>
