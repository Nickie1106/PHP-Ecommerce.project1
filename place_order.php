<?php
session_start();
include('config.php');

if(!isset($_SESSION['logged_in'])) {
    header('location: ../checkout.php?message=Please login/register to place an order');
    exit();
} else {

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
        $phone = isset($_POST['phone']);
        $city = isset($_POST['city']);
        $address = isset($_POST['address']);

        $order_cost = $_SESSION['total'];
        $order_status = "on_hold";
        $order_date = date('Y-m-d H:i:s');

        // ordersテーブルにデータを挿入
        $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bindValue(1, $order_cost, PDO::PARAM_INT);
        $stmt->bindValue(2, $order_status, PDO::PARAM_STR);
        $stmt->bindValue(3, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(4, $phone, PDO::PARAM_STR);
        $stmt->bindValue(5, $city, PDO::PARAM_STR);
        $stmt->bindValue(6, $address, PDO::PARAM_STR);
        $stmt->bindValue(7, $order_date, PDO::PARAM_STR);

        $stmt_status = $stmt->execute();

        if($stmt_status) {
            $order_id = $conn->lastInsertId();
            $_SESSION['order_id'] = $order_id;
        } else {
            echo "Error placing order: ";
            header('location: index.php');
            exit;
        }

    
        // カートにアイテムがある場合のみ処理を行う
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $product) {
                $product_id = $product['product_id'];
                $product_name = $product['product_name'];
                $product_image = $product['product_image'];
                $product_price = $product['product_price'];
                $product_quantity = $product['product_quantity'];

                $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_item->bindParam('iissdiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);

                if ($stmt_item->execute()) {
                    echo "Added product ID " . $product_id . " to order item.<br>";
                } else {
                    echo "Error adding product ID " . $product_id . ": " .   "<br>";
                }
                
            }
        } else {
            echo "Your cart is empty, no items were added to the order.<br>";
        }

        $_SESSION['order_id'] = $order_id;
        $_SESSION['cart'] = $_SESSION['cart'];
        $_SESSION['total_price'] = $order_cost;

        header('location:../payment.php?order_status=order placed successfully');
        exit();

    } else {
        echo "Error: Order not placed.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" href="<?php echo BASE_PATH; ?>layouts/assets/img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>layouts/assets/css/style.css">
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
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>contact.php">Contact us</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo BASE_PATH; ?>cart.php"><i class="fas fa-shopping-bag">
                        <?php if(isset($_SESSION['quantity']) && $_SESSION['quantity'] !=0) { ?>
                            <span class="car-qunatity"><?php echo $_SESSION['quantity']; ?></span>
                        <?php } ?>
                    </i></a>
                    <a href="<?php echo BASE_PATH; ?>account.php"><i class="fas fa-user"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Place Order Form -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Confirm your information</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="checkout-form" method="POST" action="payment.php">
            <div class="form-group checkout-small-element">
                <label>Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>" placeholder="Name" required>
            </div>
            <div class="form-group checkout-small-element">
                <label>Email</label>
                <input type="text" class="form-control" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" placeholder="Email" required>
            </div>
            <div class="form-group checkout-small-element">
                <label>Phone</label>
                <input type="text" class="form-control" name="Phone" value="<?php echo isset($_SESSION['phone']) ? $_SESSION['phone'] : ''; ?>" placeholder="Phone" required>
            </div>
            <div class="form-group checkout-small-element">
                <label>City</label>
                <input type="text" class="form-control" name="city" value="<?php echo isset($_SESSION['city']) ? $_SESSION['city'] : ''; ?>" placeholder="City" required>
            </div>
            <div class="form-group checkout-small-element">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo isset($_SESSION['address']) ? $_SESSION['address'] : ''; ?>" placeholder="Address" required>
            </div>

            
            <button type="submit" class="btn btn-success p-3 mt-3" name="place_order">Place Order</button>

        </form>
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
