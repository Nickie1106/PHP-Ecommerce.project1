<?php
session_start();
include("config.php");

// ユーザーがログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit();
}

// URLからorder_idを取得
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];  // order_idを取得
} else {
    echo "注文IDが指定されていません。";
    exit();  // order_idが指定されていない場合、処理を終了
}

// クエリを取得
$query = "
    SELECT o.order_id, o.user_id, o.order_cost, o.order_date, 
           oi.product_name, oi.product_image, oi.product_price, oi.product_quantity,
           (oi.product_price * oi.product_quantity) AS subtotal
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_id = :order_id
    ORDER BY oi.product_name ASC"; // 商品名で昇順に並び替え

$stmt = $conn->prepare($query);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT); // order_idをバインド
$stmt->execute();
$order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 合計金額を計算
$total_cost = 0;
foreach ($order_details as $order) {
    $total_cost += $order['subtotal'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文の詳細</title>
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

<main class="container mt-4">
    <h3 class="mb-4">注文詳細</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>商品名</th>
                    <th>商品画像</th>
                    <th>単価</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_details as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td>
                            <?php if (!empty($order['product_image'])): ?>
                                <img src="layouts/assets/img/<?php echo htmlspecialchars($order['product_image']); ?>" alt="商品画像" width="100" height="100">

                            <?php else: ?>
                                <p>画像なし</p>
                            <?php endif; ?>
                        </td>
                        <td>$<?php echo number_format($order['product_price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['product_quantity']); ?></td>
                        <td>$<?php echo number_format($order['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <h5 class="mt-4">合計金額: $<?php echo number_format($total_cost, 2); ?></h5>
    <a href="order_history.php" class="btn btn-warning">戻る</a>
</main>

</body>
</html>
