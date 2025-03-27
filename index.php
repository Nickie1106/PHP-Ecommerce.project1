<?php
session_start();
include('config.php'); // データベース接続を含む設定ファイルを読み込み

// ログイン状態を確認
if (!isset($_SESSION['logged_in'])) {
    header('Location: ' . BASE_PATH . 'login.php');
    exit();
}

// 商品情報を取得
$sql = "SELECT * FROM products";

// `$conn`が定義されているかチェック
if (!isset($conn)) {
    die("Database connection is not set.");
}

// クエリ実行とエラーチェック
$stmt = $conn->prepare($sql);
$stmt->execute();


//取得したデータを配列として
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
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

<!-- Home Section -->
<section id="home">
    <div class="container">
        <h5>NEW ARRIVALS</h5>
        <h1><span>Best Prices</span> This Season</h1>
        <p>Eshop offers the best products for the most affordable prices</p>
        <button onclick="window.location.href='<?php echo BASE_PATH; ?>shop.php';">Shop now</button>
    </div>
</section>

<!-- Products Display Section -->
<div class="container col-lg-9 col-md-8 col-12">
    <h3>Our Products</h3>
    <hr>
    <p>Here you can check out our products</p>
    <div class="row mx-auto container">
    <?php if (count($products) > 0) { ?>
            <?php foreach ($products as $row): ?>
                <div onclick="window.location.href='<?php echo BASE_PATH; ?>single_product.php?product_id=<?php echo $row['product_id']; ?>';" class="product text-center col-lg-3 col-md-4">
                    <div class="card h-100 text-center">
                        <?php if (!empty($row['product_image'])): ?>
                            <img class="img-fluid mb-3" src="<?php echo BASE_PATH . 'layouts/assets/img/' . htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <?php else: ?>
                            <p>No Image Available</p>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="star mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="p-name"><?php echo htmlspecialchars($row['product_name']); ?></h5>
                            <h4 class="p-price">$<?php echo htmlspecialchars($row['product_price']); ?></h4>
                            <a href="<?php echo BASE_PATH; ?>single_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-warning text-white w-100 mt-2">Buy Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        
    </div>
</div>

</div>
</body>
</html>

<?php include('layouts/footer.php'); 
    }
?>
