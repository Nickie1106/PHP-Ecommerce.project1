<?php
include_once('../config.php');
session_start();

if ($_SESSION['admin_login'] == false) {
    header("Location: " . BASE_PATH . "index.php");
    exit;
}

// クエリを取得
$query = "
    SELECT o.order_id, o.user_id, o.order_date, 
           SUM(oi.product_price * oi.product_quantity) AS total_cost
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY o.order_id ASC"; // 注文IDで昇順に並び替え

$stmt = $conn->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文管理</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1><a href="<?php echo BASE_PATH; ?>dashboard.php">管理画面</a></h1>
            </div>
            <nav class="menu-right menu">
                <a href="<?php echo BASE_PATH; ?>index.php">ログアウト</a>
            </nav>
        </div>
    </header>

    <main class="container p-5 mt-5">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>注文ID</th>
                        <th>顧客ID</th>
                        <th>合計金額</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                        <td>$<?= number_format($order['total_cost'], 2); ?></td> <!-- 合計金額のみ表示 -->
                        <td>
                            <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-warning btn-sm">詳細</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="bg-light py-3 mt-4">
        <div class="container text-center">
            <p class="mb-0">Copyright &copy; 2024 SQUARE, inc</p>
        </div>
    </footer>
</body>
</html>
