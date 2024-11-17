<?php
session_start();
include('server/connection.php');

// ユーザーがログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit();
}

// ログアウト処理
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php');
    exit();
}

// パスワード変更処理
if (isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (!isset($_SESSION['user_email'])) {
        header('location: account.php?error=ユーザーが見つかりません');
        exit();
    }

    if ($password !== $confirmPassword) {
        header('location: account.php?error=パスワードが一致しません');
        exit();
    } else if (strlen($password) < 6) {
        header('location: account.php?error=パスワードは6文字以上にしてください');
        exit();
    } else {
        $user_email = $_SESSION['user_email'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET user_password=? WHERE user_email=?");
        $stmt->bind_param('ss', $hashed_password, $user_email);

        if ($stmt->execute()) {
            header('location: account.php?message=パスワードが更新されました');
            exit();
        } else {
            header('location: account.php?error=パスワードの更新に失敗しました');
            exit();
        }
    }
}

// 注文履歴の取得
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        $orders = $stmt->get_result();
    } else {
        echo "注文情報の取得に失敗しました: " . $stmt->error;
    }
    $stmt->close();
}


include('layouts/header.php');
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
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

<!-- Account Section -->
<section class="my-5 py-5">
    <div class="row container-fluid mx-auto">

    <?php if(isset($_GET['payment_message'])) { ?>
        <p class="mt-5 text-center" style="color: green;"><?php echo $_GET['payment_message']; ?></p>
    <?php } ?>



        <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
            <p class="text-center text-success">
                <?php if (isset($_GET['register_success'])) { echo $_GET['register_success']; } ?>
            </p>
            <p class="text-center text-success">
                <?php if (isset($_GET['login_success'])) { echo $_GET['login_success']; } ?>
            </p>
            <h3 class="font-weight-bold">Account info</h3>
            <hr class="mx-auto">
            <div class="account-info">
                <p>Name<span> <?php if (isset($_SESSION['user_name'])) { echo $_SESSION['user_name']; } ?></span></p>
                <p>Email<span> <?php if (isset($_SESSION['user_email'])) { echo $_SESSION['user_email']; } ?></span></p>
                <p><a href="#orders" id="order-btn">Your orders</a></p>
                <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <form id="account-form" method="POST" action="account.php">
                <p class="text-center text-danger">
                    <?php if (isset($_GET['error'])) { echo $_GET['error']; } ?>
                </p>
                <p class="text-center text-danger">
                    <?php if (isset($_GET['message'])) { echo $_GET['message']; } ?>
                </p>
                <h3>Change Password</h3>
                <hr class="mx-auto">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Change Password" name="change_password" class="btn" id="change-pass-btn">
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Orders Section -->
<section id="orders" class="orders container my-5 py-3">
    <div class="container mt-2 text-center">
        <h2 class="font-weight-bold text-center">Your Orders</h2>
        <hr class="mx-auto" style="width: 100px;">
    </div>

    <div class="table-responsive mx-auto" style="max-width: 800px;">
        <table class="table orders-table text-center mx-auto">
            <thead>
                <tr>
                    <th>Order id</th>
                    <th>Order cost</th>
                    <th>Order status</th>
                    <th>Order date</th>
                    <th>Order details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $orders->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['order_id']; ?></td> 
                        <td><?php echo $row['order_cost']; ?></td> 
                        <td><?php echo $row['order_status']; ?></td> 
                        <td><?php echo $row['order_date']; ?></td> 
                        <td>
                            <a href="order_details.php?order_id=<?php echo $row['order_id']; ?>&order_status=<?php echo $row['order_status']; ?>" class="btn order-details-btn">details</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
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


</body>
</html>
