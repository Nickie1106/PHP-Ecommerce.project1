<?php 
session_start();
if (isset($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {
    header("Location: dashboard.php");
    exit();
}

include('layouts/header.php'); 

// データベース接続
$servername = "localhost";
$username = "nishimura";
$password = "nishimura";
$dbname = "nishimura_php_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーチェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// productsテーブルからすべての製品を取得
$sql = "SELECT * FROM products";
$products = $conn->query($sql);

// BASE_URLを定義（実際のURLに合わせて設定）
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/project-php/');
}
?>

<!-- Home Section -->
<section id="home">
    <div class="container">
        <h5>NEW ARRIVALS</h5>
        <h1><span>Best Prices</span> This Season</h1>
        <p>Eshop offers the best products for the most affordable prices</p>
        <button>Shop now</button>
    </div>
</section>

<!-- Main Brand Section -->
<section id="main-brand" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>Main Brand</h3>
        <hr>
        <p>Here is the main Brand</p>
    </div>
</section>

<!-- Brand Section -->
<section id="brand" class="container">
    <div class="row">
        <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="<?php echo BASE_URL; ?>layouts/assets/img/brand.logo1.png" alt="brand1">
        <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="<?php echo BASE_URL; ?>layouts/assets/img/brand.logo2.png" alt="brand2">
        <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="<?php echo BASE_URL; ?>layouts/assets/img/brand.logo3.png" alt="brand3">
        <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="<?php echo BASE_URL; ?>layouts/assets/img/brand.logo4.png" alt="brand4">
    </div>
</section>

<!-- Products Display Section -->
<div class="container col-lg-9 col-md-8 col-12">
    <h3>Our Products</h3>
    <hr>
    <p>Here you can check out our products</p>
    <div class="row mx-auto container">
        <?php if ($products->num_rows > 0) { // 製品が存在する場合 ?>
            <?php while ($row = $products->fetch_assoc()) { ?>
                <div onclick="window.location.href='single_product.php?product_id=<?php echo $row['product_id']; ?>';" class="product text-center col-lg-3 col-md-4">
                    <div class="card h-100 text-center">
                        <img class="img-fluid mb-3" src="<?php echo BASE_URL; ?>layouts/assets/img/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
                        <div class="card-body">
                            <div class="star mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
                            <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
                            <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-warning text-white w-100 mt-2">Buy Now</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { // 製品が存在しない場合 ?>
            <p>No products found.</p>
        <?php } ?>
    </div>
</div>

<nav aria-label="Page navigation example" class="mx-auto">
    <ul class="pagination mt-5 mx-auto">
    </ul>
</nav>

<?php include('layouts/footer.php'); ?>
