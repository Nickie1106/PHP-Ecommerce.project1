<?php

session_start();

include('connection.php');

if(isset($_GET['transaction_id']) && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $order_status = "paid";
    $transaction_id = $_GET['transaction_id'];
    $user_id = $_GET['user_id'];


    $stmt = $conn->prepare("UPDATE orders GET order_status=? WHERE order_id=?");
    $stmt->bind_param('si', $order_status, $order_id);

    $stmt->execute();

    $stmt1 = $conn->prepare("INSERT INTO payments (order_id, user_id, transaction_id
                            VALUES (?,?,?) ;");

    $stmt1->bind_param('iii', $order_id, $user_id, $transaction_id);
       
    $stmt->execute();

    header("location: ::/account.php?payment_message=paid successfully, Thank you for shopping with us.");

} else {
    header("location: index.php");
    exit();

}





?>