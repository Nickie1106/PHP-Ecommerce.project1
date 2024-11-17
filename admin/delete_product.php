<?php

session_start();
if ( $_SESSION['admin_login'] === false) {
    header("location:./index.php");
    exit;
}

if (isset($_GET['id'])) {
    try{
        $dbh = new PDO("mysql:host=localhost;dbname=nishimura_php_project", "nishimura", "nishimura");
        $stmt = $dbh->prepare("DELETE FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        header("location: product_list.php");
        exit;
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
    }
}

?>