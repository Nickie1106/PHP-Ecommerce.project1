<?php
include_once('../config.php');
session_start();

if ($_SESSION['admin_login'] == false) {
    header("Location: " . BASE_PATH . "index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1><a href="<?php echo BASE_PATH; ?>admin/dashboard.php">管理画面</a></h1>
            </div>
            <nav class="menu-right menu">
                <a href="<?php echo BASE_PATH; ?>index.php">ログアウト</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="wrapper">
            <div class="container">
                <div class="wrapper-title">
                    <h3>商品管理</h3>
                </div>
                <div class="list">
                    <table>
                        <thead>
                            <tr>
                                <th>商品id</th>
                                <th>商品名</th>
                                <th>金額</th>
                                <th>商品画像</th>
                                <th>商品詳細</th>
                                <th>在庫数</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['product_price']); ?>$</td>
                                <td>
                                    <?php if (!empty($product['product_image'])): ?>
                                        <img src="assets/img/<?php echo htmlspecialchars($product['product_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="商品画像" width="100" height="100">
                                        <?php else: ?>
                                        <p>画像なし</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                                <td><?php echo htmlspecialchars($product['product_quantity']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_PATH; ?>admin/edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-green">編集</a>
                                    <a href="<?php echo BASE_PATH; ?>admin/delete_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-red" onclick="return confirm('本当に削除しますか？');">削除</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="container">
            <p>Copyright &copy; 2018 SQUARE, inc</p>
        </div>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>
</html>
