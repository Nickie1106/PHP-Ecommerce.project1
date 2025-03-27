<?php
session_start();
include('config.php');

// カートが存在しない場合に初期化
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// ページ番号とオフセットの設定
$page_no = isset($_GET['page_no']) && is_numeric($_GET['page_no']) ? (int)$_GET['page_no'] : 1;
$total_records_per_page = 10;
$offset = ($page_no - 1) * $total_records_per_page;

// フィルタリング用変数
$category = $_POST['category'] ?? null;
$price = $_POST['price'] ?? null;

try {
    // データベース接続確認
    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    // 商品リストの取得
    if (isset($_POST['search']) && $category && $price) {
        // フィルタ条件がある場合
        $stmt = $conn->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM products WHERE product_category = :category AND product_price <= :price LIMIT :offset, :limit");
        $stmt->bindParam(':category', $category, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    } else {
        // 全商品を取得
        $stmt = $conn->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM products LIMIT :offset, :limit");
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $total_records_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 総商品数を取得
    $total_stmt = $conn->query("SELECT FOUND_ROWS() as total");
    $total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_no_of_pages = ceil($total_records / $total_records_per_page);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
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

<section id="shop" class="container mt-5 pt-5">
    <div class="row">
        <!-- サイドバー -->
        <div class="col-lg-3 col-md-4 col-12 mb-4">
            <form action="shop.php" method="post">
                <h5>Filter Products</h5>
                <hr>
                <!-- カテゴリ -->
                <p>Category</p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="Shoes" id="shoes">
                    <label class="form-check-label" for="shoes">Shoes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="Bags" id="bags">
                    <label class="form-check-label" for="bags">Bags</label>
                </div>
                <!-- 価格 -->
                <p>Price</p>
                <input type="range" name="price" class="form-range" min="1" max="1000" id="priceRange">
                <div class="d-flex justify-content-between"><span>$1</span><span>$1000</span></div>
                <button type="submit" name="search" class="btn btn-primary w-100 mt-3">Search</button>
            </form>
        </div>

        <!-- 商品一覧 -->
        <div class="col-lg-9 col-md-8 col-12">
            <h3>Our Products</h3>
            <hr>
            <div class="row mx-auto container">
                <?php if ($products): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 text-center mb-4">
                            <div class="card h-100 text-center">
                                <img class="img-fluid mb-3" src="layouts/assets/img/<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <div class="card-body">
                                    <h5><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                    <h4>$<?php echo htmlspecialchars($product['product_price']); ?></h4>
                                    <a href="single_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-warning text-white w-100 mt-2">Buy now</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ページネーション -->
<nav>
    <ul class="pagination justify-content-center mt-4">
        <li class="page-item <?php echo $page_no <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_no=1">First</a>
        </li>
        <li class="page-item <?php echo $page_no <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_no=<?php echo $page_no - 1; ?>">Previous</a>
        </li>
        <li class="page-item <?php echo $page_no >= $total_no_of_pages ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_no=<?php echo $page_no + 1; ?>">Next</a>
        </li>
        <li class="page-item <?php echo $page_no >= $total_no_of_pages ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page_no=<?php echo $total_no_of_pages; ?>">Last</a>
        </li>
    </ul>
</nav>

<!-- フッター -->
<?php include('layouts/footer.php'); ?>
</body>
</html>
