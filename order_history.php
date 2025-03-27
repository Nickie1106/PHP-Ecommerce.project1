<?php
session_start();
include("config.php");


if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// クエリを取得
$query = "
    SELECT o.order_id, o.user_id, o.order_cost, o.order_date, 
           SUM(oi.product_price * oi.product_quantity) AS total_cost
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.user_id = :user_id  
    GROUP BY o.order_id
    ORDER BY o.order_id ASC"; // 注文IDで昇順に並び替え

$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文履歴</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>layouts/assets/css/style.css">
</head>

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

<div class="container p-5 mt-5">
    <h3>注文履歴</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ユーザーID</th>
                <th>注文日</th>
                <th>合計金額</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']); ?></td>
                    <td><?= htmlspecialchars($order['order_date']); ?></td>
                    <td>$<?= number_format($order['order_cost'], 2); ?></td>
                    <td>
                        <a href="order_history_detail.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-warning btn-sm">詳細</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



</body>
</html>
