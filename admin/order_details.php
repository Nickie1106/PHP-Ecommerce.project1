<?php
include_once("../config.php");
session_start();

if ($_SESSION['admin_login'] == false) {
    header("location: " . BASE_PATH . "index.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "注文IDが指定されていません。";
    exit;
}

$order_id = $_GET['order_id'];

// 商品詳細クエリ
$query = "
    SELECT product_name, product_image, product_price, product_quantity
    FROM order_items
    WHERE order_id = :order_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 合計金額計算
$total_cost = array_reduce($order_details, function ($carry, $item) {
    return $carry + ($item['product_price'] * $item['product_quantity']);
}, 0);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文詳細</title>
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
                    <?php foreach ($order_details as $order_detail): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order_detail['product_name']); ?></td>
                            <td>
                                <?php if (!empty($order_detail['product_image'])): ?>
                                    <img src="assets/img/<?php echo htmlspecialchars($order_detail['product_image']); ?>" alt="商品画像" width="100" height="100">
                                <?php else: ?>
                                    <p>画像なし</p>
                                <?php endif; ?>
                            </td>
                            <td>$<?php echo number_format($order_detail['product_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order_detail['product_quantity']); ?></td>
                            <td>$<?php echo number_format($order_detail['product_price'] * $order_detail['product_quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <h5 class="mt-4">合計金額: $<?php echo number_format($total_cost, 2); ?></h5>
        <a href="order_list.php" class="btn btn-warning">戻る</a>
    </main>
</body>
</html>
