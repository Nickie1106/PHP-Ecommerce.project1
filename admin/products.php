<?php
include_once('../config.php');  // config.phpが1つ上のディレクトリにある場合（パスを正しく設定してください）

session_start();

if ($_SESSION['admin_login'] == false) {
    header("Location: " . BASE_URL . "index.php");  // BASE_URLを使用
    exit;
}

// DB接続
try {
    $dbh = new PDO("mysql:host=localhost;dbname=nishimura_php_project", "nishimura", "nishimura");
} catch (PDOException $e) {
    var_dump($e->getMessage());
    exit;
}

$stmt = $dbh->prepare("SELECT * FROM products");
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

    <!-- css -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1><a href="<?php echo BASE_URL; ?>dashboard.php">管理画面</a></h1> 
            </div>

            <nav class="menu-right menu">
                <a href="<?php echo BASE_URL; ?>logout.php">ログアウト</a> 
            </nav>
        </div>
    </header>
    <main>
        <div class="wrapper">
            <div class="container">
                <div class="wrapper-title">
                    <h3>商品管理</h3>
                </div>
                <button type="button" class="btn btn-blue" onclick="location.href='<?php echo BASE_URL; ?>create_news.php'">投稿する</button> <!-- BASE_URLを使用 -->
                <div class="list">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>商品名</th>
                                <th>説明文</th>
                                <th>単価</th>
                                <th>画像</th>
                                <th>更新日時</th>
                                <th>作成日時</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo isset($product['product_id']) ? htmlspecialchars($product['product_id']) : 'N/A'; ?></td>
                                <td><?php echo isset($product['product_name']) ? htmlspecialchars($product['product_name']) : 'N/A'; ?></td>
                                <td><?php echo isset($product['product_description']) ? htmlspecialchars($product['product_description']) : 'N/A'; ?></td>
                                <td><?php echo isset($product['product_price']) ? htmlspecialchars($product['product_price']) : 'N/A'; ?></td>
                                <td>
                                    <img src="img/<?php echo isset($product['product_image']) ? htmlspecialchars($product['product_image']) : 'default.jpg'; ?>" alt="Product Image" width="100">
                                </td>
                                <td><?php echo isset($product['updated_at']) ? htmlspecialchars($product['updated_at']) : 'N/A'; ?></td>
                                <td><?php echo isset($product['created_at']) ? htmlspecialchars($product['created_at']) : 'N/A'; ?></td>
                                <td>
                                    <button class="btn btn-green" onclick="location.href='<?php echo BASE_URL; ?>admin/edit_product.php?id=<?php echo $product['product_id']; ?>'">編集</button> <!-- BASE_URLを使用 -->
                                    <button class="btn btn-red" onclick="location.href='<?php echo BASE_URL; ?>delete_product.php?id=<?php echo $product['product_id']; ?>'">削除</button> <!-- BASE_URLを使用 -->
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
