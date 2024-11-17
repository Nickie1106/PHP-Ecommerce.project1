<?php
session_start(); 

if ($_SESSION['admin_login'] === false) {
    header("location:./index.php");
    exit;
}

//初期化
$product = [
    'product_id' => '',
    'product_name' => '',
    'product_price' => '',
    'product_description' => '',
    'produc_quantity' => '',
];

//DB接続と商品情報取得
if (isset($_GET['id'])) {
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=nishimura_php_project", "nishimura", "nishimura");
        $stmt = $dbh->prepare("SELECT * FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
    }
}

//フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
        $stmt = $dbh->prepare("
            UPDATE products
            SET
            product_name = :product_name,
            product_price = :product_price,
            product_description = :product_description,
            product_quantity = :product_quantity,
            WHERE product_id = :product_id
        ");
        $stmt->bindParam(':product_name', $_POST['product_name']);
        $stmt->bindParam(':product_price', $_POST['product_price']);
        $stmt->bindParam(':product_description', $_POST['product_description']);
        $stmt->bindParam(':product_quantity', $_POST['product_quantity']);
        $stmt->bindParam(':product_id', $_POST['product_id'], PDO::PARAM_INT);
        $stmt->execute();
        header("location: product_list.php");
        exit;
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
          
    }
}
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- 明るい緑の背景を設定 -->
    <div class="container py-5 mt-5 bg-success text-white">
        <h1>商品編集</h1>
        <form action="edit_product.php?id=<?php echo htmlspecialchars($_GET['id']); ?>" method="post">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
            <div class="form-group">
                <label for="product_name">商品名</label>
                <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_price">金額</label>
                <input type="number" name="product_price" class="form-control" value="<?php echo htmlspecialchars($product['product_price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_description">商品詳細</label>
                <textarea name="product_description" class="form-control" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="product_quantity">在庫数</label>
                <input type="number" name="product_quantity" class="form-control" value="<?php echo htmlspecialchars($product['product_quantity']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</body>
</html>