<?php

session_start();

include 'server/connection.php';

if(!isset($_SESSION['logged_in'])) {
  header('location: ../checkout.php?message=Please login/register to place an order');
  exit();
}else{



if (!isset($conn)) {
  die("Database connection failed.");
}

// ユーザーがログインしているか確認
if (!isset($_SESSION['user_id'])) {
  header('location: login.php');
  exit();
}

// ユーザーIDを取得
$user_id = $_SESSION['user_id'];

if (isset($_POST['place_order'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';

    $order_cost = $_SESSION['total'];
    $order_status = "on_hold";
    $order_date = date('Y-m-d H:i:s');

    // ordersテーブルにデータを挿入
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isiisss', $order_cost, $order_status, $user_id, $phone, $city, $address, $order_date);

    $stmt_status = $stmt->execute();

    if(!$stmt_status) {
      header('location: index.php');
      exit();
    }

    // 実行して、成功したらorder_idを取得
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        echo "Order placed successfully. Order ID: " . $order_id . "<br>";

        // 各商品をorder_itemsテーブルに挿入
        foreach ($_SESSION['cart'] as $key => $product) {
            $product_id = $product['product_id'];
            $product_name = $product['product_name'];
            $product_image = $product['product_image'];
            $product_price = $product['product_price'];
            $product_quantity = $product['product_quantity'];

            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_item->bind_param('iissdiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);

            if ($stmt_item->execute()) {
                echo "Added product ID " . $product_id . " to order item.<br>";
            } else {
                echo "Error adding product ID " . $product_id . ": " . $stmt_item->error . "<br>";
            }
            $stmt_item->close();
        }

        $_SESSION['order_id'] = $order_id;

        header('location:../payment.php?order_status=order placed successfully');
        exit();

    } else {
        echo "Error placing order: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>


<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
        <div class="container">
         <h5>8</h5>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="shop.php">Shop</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Blog</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="contact.php">Contact us</a>
              </li>
              <li class="nav-item">
                <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="account.php"><i class="fas fa-user"></i></a>

              </li>
              
            </ul>
          </div>
        </div>
      </nav>
   




    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVRUcI6KD3fQQ4hxLrv+2K6KWfquk9mFY5P0j4fsN2Xo3nr/YkT75sA0cUqgKn7g" crossorigin="anonymous"></script>
</body>
</html>