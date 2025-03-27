<?php  
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('ログインしてください。'); window.location.href='login.php';</script>";
    exit;
}

$total = $_SESSION['total'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');
    $payment_status = 'Pending';

    $user_phone = $_POST['user_phone'] ?? '';
    $user_city = $_POST['user_city'] ?? '';
    $user_address = $_POST['user_address'] ?? '';

    if (empty($user_phone) || empty($user_city) || empty($user_address)) {
        echo "<script>alert('すべての配送情報を入力してください。'); window.location.href='payment.php';</script>";
        exit;
    }

    try {
        // orders テーブルにデータを挿入
        $query = "INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) 
                  VALUES (:order_cost, 'Pending', :user_id, :user_phone, :user_city, :user_address, :order_date)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':order_cost' => $total,
            ':user_id' => $user_id,
            ':user_phone' => $user_phone,
            ':user_city' => $user_city,
            ':user_address' => $user_address,
            ':order_date' => $order_date,
        ]);

        $order_id = $conn->lastInsertId();

        // order_items と payments のデータ挿入
        $item_query = "INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) 
                       VALUES (:order_id, :product_id, :product_name, :product_image, :product_price, :product_quantity, :user_id, :order_date)";
        $payment_query = "INSERT INTO payments (order_id, user_id, transaction_id, product_id) 
                          VALUES (:order_id, :user_id, :transaction_id, :product_id)";
        $item_stmt = $conn->prepare($item_query);
        $payment_stmt = $conn->prepare($payment_query);

        $transaction_id = 'txn_' . uniqid();

        foreach ($_SESSION['cart'] as $item) {
            $item_stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $item['product_id'],
                ':product_name' => $item['product_name'],
                ':product_image' => $item['product_image'],
                ':product_price' => $item['product_price'],
                ':product_quantity' => $item['product_quantity'],
                ':user_id' => $user_id,
                ':order_date' => $order_date,
            ]);

            $payment_stmt->execute([
                ':order_id' => $order_id,
                ':user_id' => $user_id,
                ':transaction_id' => $transaction_id,
                ':product_id' => $item['product_id'],
            ]);
        }

        // カートをクリア
        $_SESSION['cart'] = [];
        $_SESSION['total'] = 0;

        echo "<script>alert('注文が成功しました！'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('注文に失敗しました。もう一度試してください。');</script>";
        error_log($e->getMessage());
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>layouts/assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact us</a></li>
                <li class="nav-item"><a href="cart.php"><i class="fas fa-shopping-bag"></i></a><a href="account.php"><i class="fas fa-user"></i></a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Payment Section -->
<section class="payment container my-5 pt-5">
    <h2 class="text-center mb-4">Payment Summary</h2>
    <div class="row">
        <!-- Cart Items Table -->
        <div class="col-lg-8 col-md-12">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>商品</th>
                        <th>数量</th>
                        <th>商品ID</th>
                        <th>合計金額</th>
                    </tr>
                </thead>
                <tbody>
                <?php
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        foreach ($cart as $item) {
            ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo BASE_PATH . 'layouts/assets/img/' . $item['product_image']; ?>" alt="Product Image" style="width: 80px; height: auto; margin-right: 10px;">
                                    <span><?php echo $item['product_name']; ?></span>
                                </div>
                            </td>
                            <td><?php echo $item['product_quantity']; ?></td>
                            <td><?php echo $item['product_id']; ?></td>
                            <td>$<?php echo number_format($item['product_quantity'] * $item['product_price'], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4 col-md-12 mt-4 mt-lg-0">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>オーダー内容</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="d-flex justify-content-between">
                            <span>Total Items:</span>
                            <span><?php echo count($cart); ?> item(s)</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Total Amount:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </li>
                    </ul>


                    <form method="POST" action="payment.php">
                        <div class="form-group">
                            <label for="user_phone">Phone:</label>
                            <input type="text" id="user_phone" name="user_phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="user_city">City:</label>
                            <input type="text" id="user_city" name="user_city" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="user_address">Address:</label>
                            <input type="text" id="user_address" name="user_address" class="form-control" required>
                        </div>
                        <input type="hidden" name="total" value="<?php echo $total; ?>">
                        <button type="submit" class="btn btn-success text-white">注文を確定する</button>
                    </form>


                    <div class="col-lg-4 col-md-12 mt-4 mt-lg-0">
                        <form method="POST" action="index.php">
                            <input type="hidden" name="total" value="<?php echo $total; ?>">
                            <button type="submit" class="btn btn-warning text-white">ホームに戻る</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="mt-5 py-5">
    <div class="row container mx-auto pt-5">
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <img src="<?php echo BASE_PATH; ?>layouts/assets/img/8logo.png" alt="8logo">
            <p class="pt-3">We provide the best products for the most affordable prices</p>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Featured</h5>
            <ul class="text-uppercase">
                <li><a href="#">Men</a></li>
                <li><a href="#">Women</a></li>
                <li><a href="#">Boys</a></li>
                <li><a href="#">Girls</a></li>
                <li><a href="#">New Arrivals</a></li>
                <li><a href="#">Clothes</a></li>
            </ul>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Contact us</h5>
            <div>
                <h6 class="text-uppercase">Address</h6>
                <p>1234 Street, City, Country</p>
            </div>
            <div>
                <h6 class="text-uppercase">Phone</h6>
                <p>123 456 7890</p>
            </div>
            <div>
                <h6 class="text-uppercase">Email</h6>
                <p>info@eshop.com</p>
            </div>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Instagram</h5>
            <div class="row">
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_PATH; ?>layouts/assets/img/img.clothes1.jpg" alt="clothes1">
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_PATH; ?>layouts/assets/img/img.clothes2.jpg" alt="clothes2">
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_PATH; ?>layouts/assets/img/img.clothes3.jpg" alt="clothes3">
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_PATH; ?>layouts/assets/img/img.clothes4.jpg" alt="clothes4">
            </div>
        </div>
    </div>

    <div class="copyright mt-5">
        <div class="row container mx-auto">
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
                <img src="<?php echo BASE_PATH; ?>layouts/assets/img/payment.logo.png" alt="Payment Logo">
            </div>
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4 text-nowrap mb-2">
                <p>eCommerce @ 2025 All Right Reserved</p>
            </div>
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVRUcI6KD3fQQ4hxLrv+2K6KWfquk9mFY5P0j4fsN2Xo3nr/YkT75sA0cUqgKn7g" crossorigin="anonymous"></script>
</body>
</html>
