<?php  
session_start(); // セッションの開始はファイルの一番上に配置

$servername = "localhost"; 
$username = "nishimura"; 
$password = "nishimura"; 
$dbname = "nishimura_php_project"; 

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// カートの合計金額を計算する関数
function calculateTotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['product_price'] * $item['product_quantity'];
        }
    }
    return $total;
}

// 商品追加処理
if (isset($_POST['add_to_cart'])) {  // Add to Cartボタンの確認
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = isset($_POST['product_quantity']) ? (int)$_POST['product_quantity'] : 1;

    $item_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id && $item['product_image'] == $product_image) {
            $item['product_quantity'] += $product_quantity;
            $item_exists = true;
            break;
        }
    }

    if (!$item_exists) {
        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_image' => $product_image,
            'product_quantity' => $product_quantity,
        );
        $_SESSION['cart'][] = $product_array;
    }
}

// 商品削除
if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];
    $product_image = $_POST['product_image'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $product_id && $item['product_image'] == $product_image) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

// 数量編集
if (isset($_POST['edit_quantity'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = (int)$_POST['product_quantity'];
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id) {
            $item['product_quantity'] = $product_quantity;
            break;
        }
    }
}

// 合計金額を取得
$total = calculateTotal();
$_SESSION['total'] = $total;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="/layouts/assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
        <h5>8</h5>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact us</a></li>
                <li class="nav-item">
                    <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
                    <a href="account.php"><i class="fas fa-user"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Cart Section -->
<section class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bold">Your cart</h2>
        <hr>
    </div>

    <table class="mt-5 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>

        <?php 
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        foreach($cart as $key => $value) {
            ?>
        <tr>
            <td>
                <div class="product-info">
                    <img src="layouts/assets/img/<?php echo $value['product_image']; ?>" alt="Product Image"/>
                    <div>
                        <p><?php echo $value['product_name']; ?></p>
                        <small><span>$</span><?php echo $value['product_price']; ?></small>
                        <br>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                            <input type="hidden" name="product_image" value="<?php echo $value['product_image']; ?>">
                            <button type="submit" name="remove_product" class="remove-link">Remove</button>
                        </form>
                    </div>
                </div>
            </td>

            <td>
                <form method="POST" action="cart.php" style="display: flex; align-items: center;">
                    <input type="hidden" name="product_id" value="<?php echo $value['product_id'];?>">
                    <input type="number" name="product_quantity" value="<?php echo $value['product_quantity'];?>" class="form-control" style="width: 80px;">
                    <button type="submit" name="edit_quantity" class="btn btn-warning">Update</button>
                </form>
            </td>

            <td>
                <span>$</span>
                <span class="product-price"><?php echo $value['product_quantity'] * $value['product_price']; ?></span>
            </td>
        </tr>

        <?php } ?>

        <div class="cart-total">
            <table>
                <tr>
                    <td>Total</td>
                    <td>$<?php echo number_format($total, 2);?></td>
                </tr>
            </table>
        </div>

        <div class="checkout-container">
            <form method="POST" action="checkout.php">
                <input type="submit" class="btn btn-success" value="Checkout" name="checkout">
            </form>
        </div>
    </table>
</section>

<!-- Footer -->
<footer class="mt-5 py-5">
    <div class="row container mx-auto pt-5">
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <img src="<?php echo BASE_URL; ?>layouts/assets/img/8logo.png" alt="8logo">
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
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_URL; ?>layouts/assets/img/img.clothes1.jpg" alt="clothes1">
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_URL; ?>layouts/assets/img/img.clothes2.jpg" alt="clothes2">
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_URL; ?>layouts/assets/img/img.clothes3.jpg" alt="clothes3">
                <img class="img-fluid w-25 h-100 m-2" src="<?php echo BASE_URL; ?>layouts/assets/img/img.clothes4.jpg" alt="clothes4">
            </div>
        </div>
    </div>

    <div class="copyright mt-5">
        <div class="row container mx-auto">
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
                <img src="<?php echo BASE_URL; ?>layouts/assets/img/payment.logo.png" alt="Payment Logo">
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

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
