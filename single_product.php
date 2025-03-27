<?php
include('config.php');

// 商品IDを取得
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;

if (!$product_id) {
    die("無効な商品IDです。");
}

$product = null;

// `$conn`が定義されているかチェック
if (!isset($conn)) {
    die("データベース接続が設定されていません。");
}

// 商品情報を取得
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = :product_id LIMIT 1");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("商品が見つかりません。");
    }
} catch (PDOException $e) {
    die("データベースエラー: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?></title>
    <link rel="icon" href="<?php echo BASE_PATH; ?>layouts/assets/img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
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
                        <?php if(isset($_SESSION['quantity']) && $_SESSION['quantity'] != 0) { ?>
                            <span class="car-qunatity"><?php echo $_SESSION['quantity']; ?></span>
                        <?php } ?>
                    </i></a>
                    <a href="<?php echo BASE_PATH; ?>account.php"><i class="fas fa-user"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<section class="container single_product my-5 pt-5">
    <div class="row mt-5">
        <form method="POST" action="<?php echo BASE_PATH; ?>cart.php">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <input type="hidden" name="product_image" value="<?php echo $product['product_image']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $product['product_price']; ?>">

            <div class="col-lg-5 col-md-6 col-sm-12">
                <img class="img-fluid w-100 pb-1" src="<?php echo BASE_PATH; ?>layouts/assets/img/<?php echo $product['product_image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            </div>
            <div class="col-lg-6 col-md-12 col-12">
                <h6>Category: Clothes/Shoes</h6>
                <h3 class="py-4"><?php echo $product['product_name']; ?></h3>
                <h2>$<?php echo $product['product_price']; ?></h2>
                <input type="number" name="product_quantity" value="1" min="1" class="form-control w-50 mt-3">
                <button type="submit" name="add_to_cart" class="btn btn-warning text-white p-3 mt-3">Add to Cart</button>
                <hr class="mt-3" style="border-top: 2px solid #ddd;">
            </div>
        </form>
        <br>
        <div class="col-lg-6 col-md-12 col-12">
            <h4 class="pt-3 mb-3">Product details</h4>
            <p><?php echo htmlspecialchars($product['product_description']); ?></p>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
