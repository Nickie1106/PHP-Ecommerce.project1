<?php include('layouts/header.php'); ?>

<?php
include('server/connection.php');

$category = $_POST['category'] ?? null;
$price = $_POST['price'] ?? null;
$page_no = isset($_GET['page_no']) && is_numeric($_GET['page_no']) ? (int)$_GET['page_no'] : 1;
$total_records_per_page = 8;
$offset = ($page_no - 1) * $total_records_per_page;

// 商品の総数とページ情報の取得
if (isset($_POST['search']) && $category && $price) {
    // 1. フィルタされた商品数を取得
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE product_category = ? AND product_price <= ?");
    $stmt->bind_param("si", $category, $price);
    $stmt->execute();
    $stmt->bind_result($total_records);
    $stmt->fetch();
    $stmt->close();

    // 2. フィルタに基づく商品を取得
    $stmt2 = $conn->prepare("SELECT * FROM products WHERE product_category = ? AND product_price <= ? LIMIT ?, ?");
    $stmt2->bind_param("siii", $category, $price, $offset, $total_records_per_page);
    $stmt2->execute();
    $products = $stmt2->get_result();
} else {
    // 検索条件なしで全商品を取得
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products");
    $stmt->execute();
    $stmt->bind_result($total_records);
    $stmt->fetch();
    $stmt->close();

    // ページネーション付きで全商品を取得
    $stmt2 = $conn->prepare("SELECT * FROM products LIMIT ?, ?");
    $stmt2->bind_param("ii", $offset, $total_records_per_page);
    $stmt2->execute();
    $products = $stmt2->get_result();
}

$total_no_of_pages = ceil($total_records / $total_records_per_page);
$previous_page = $page_no > 1 ? $page_no - 1 : 1;
$next_page = $page_no < $total_no_of_pages ? $page_no + 1 : $total_no_of_pages;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="layouts/assets/css/style.css">
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

    <!-- Product Filter and Display Section -->
    <section id="shop" class="container mt-5 pt-5">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 col-md-4 col-12 mb-4">
                <h5>Search Products</h5>
                <hr>
                <form action="shop.php" method="post">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <p>Category</p>
                        <div class="form-check"><input class="form-check-input" value="shoes" type="radio" name="category" id="category_one"><label class="form-check-label" for="category_one">Shoes</label></div>
                        <div class="form-check"><input class="form-check-input" value="Coats" type="radio" name="category" id="categoryCoats"><label class="form-check-label" for="categoryCoats">Coats</label></div>
                        <div class="form-check"><input class="form-check-input" value="Watches" type="radio" name="category" id="categoryWatches"><label class="form-check-label" for="categoryWatches">Watches</label></div>
                        <div class="form-check"><input class="form-check-input" value="Bags" type="radio" name="category" id="categoryBags"><label class="form-check-label" for="categoryBags">Bags</label></div>
                        <h6 class="mt-4">Price</h6>
                        <input type="range" name="price" class="form-range" min="1" max="1000" id="priceRange">
                        <div class="d-flex justify-content-between"><span>1</span><span>1000</span></div>
                        <button type="submit" name="search" class="btn btn-primary w-100 mt-3">Search</button>
                    </div>
                </form>
            </div>

            <!-- Products Display Section -->
            <div class="col-lg-9 col-md-8 col-12">
                <h3>Our Products</h3>
                <hr>
                <p>Here you can check out our products</p>
                <div class="row mx-auto container">
                    <?php while ($row = $products->fetch_assoc()) { ?>
                        <div onclick="window.location.href='single_product.php?product_id=<?php echo $row['product_id']; ?>';" class="product text-center col-lg-3 col-md-4">
                            <div class="card h-100 text-center">
                                <img class="img-fluid mb-3" src="layouts/assets/img/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
                                <div class="card-body">
                                    <div class="star mb-2">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                    <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
                                    <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
                                    <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-warning text-white w-100 mt-2">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation example" class="mx-auto">
            <ul class="pagination mt-5 mx-auto">
                <li class="page-item <?php if ($page_no <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if ($page_no > 1) { echo "?page_no=" . $previous_page; } ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_no_of_pages; $i++) { ?>
                    <li class="page-item <?php if ($page_no == $i) { echo 'active'; } ?>">
                        <a class="page-link" href="?page_no=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?php if ($page_no >= $total_no_of_pages) { echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if ($page_no < $total_no_of_pages) { echo "?page_no=" . $next_page; } ?>">Next</a>
                </li>
            </ul>
        </nav>
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

<?php include('layouts/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
