<?php
// config.phpのインクルードパスを確認して正しく記述
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1><a href="<?php echo BASE_URL; ?>dashboard.php">管理画面</a></h1> <!-- BASE_URLを使用 -->
            </div>

            <nav class="menu-right menu">
                <a href="<?php echo BASE_URL; ?>logout.php">ログアウト</a> <!-- BASE_URLを使用 -->
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
                                <th>商品id</th>
                                <th>商品名</th>
                                <th>金額</th>
                                <th>商品詳細</th>
                                <th>在庫数</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['product_price']); ?>円</td>
                                <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-green">編集</a> <!-- BASE_URLを使用 -->
                                    <a href="<?php echo BASE_URL; ?>delete_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-red" onclick="return confirm('本当に削除しますか？');">削除</a> <!-- BASE_URLを使用 -->
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
