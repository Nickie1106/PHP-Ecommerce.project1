<?php
session_start();

if ($_SESSION['admin_login'] == false) {
    header("Location: ./index.php");
    exit;
}

$order_id = isset($_GET['id']) ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'utf-8') : '';
if ($order_id == '') {
    header('Location: ./orders.php');
    exit;
}

// DB接続
try {
    $dbh = new PDO("mysql:host=localhost;dbname=nishimura_php_project", "nishimura", "nishimura");
} catch (PDOException $e) {
    var_dump($e->getMessage());
    exit;
}

// order_items テーブルと products テーブルを結合して注文商品を取得
$stmt = $dbh->prepare("SELECT oi.order_id, p.product_name, oi.num, p.product_price, p.product_description
                       FROM order_items AS oi
                       JOIN products AS p ON oi.product_id = p.product_id
                       WHERE oi.order_id = :order_id");
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$order_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>注文商品の詳細</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1><a href="dashboard.php">管理画面</a></h1>
            </div>
            <nav class="menu-right menu">
                <a href="logout.php">ログアウト</a>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <h3>注文商品の詳細</h3>
            <table>
                <thead>
                    <tr>
                        <th>注文ID</th>
                        <th>商品名</th>
                        <th>個数</th>
                        <th>金額</th>
                        <th>商品詳細</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_products as $order_product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order_product['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order_product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($order_product['num']); ?></td>
                        <td><?php echo htmlspecialchars($order_product['product_price']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($order_product['product_description'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
