<?php
session_start(); 

include('../config.php'); // config.phpの正しいパス

if ($_SESSION['admin_login'] === false) {
    header("location: " . BASE_PATH . "index.php");
    exit;
}

// 初期化
$product = [
    'product_id' => '',
    'product_name' => '',
    'product_price' => '',
    'product_description' => '',
    'product_quantity' => '',
];


// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("
            UPDATE products
            SET
                product_name = :product_name,
                product_price = :product_price,
                product_description = :product_description,
                product_quantity = :product_quantity
            WHERE product_id = :product_id
        ");
        $stmt->bindParam(':product_name', $_POST['product_name']);
        $stmt->bindParam(':product_price', $_POST['product_price']);
        $stmt->bindParam(':product_description', $_POST['product_description']);
        $stmt->bindParam(':product_quantity', $_POST['product_quantity']);
        $stmt->bindParam(':product_id', $_POST['product_id'], PDO::PARAM_INT);
        $stmt->execute();
        header("location: " . BASE_PATH . "admin/product_list.php"); // 編集後の商品リストページへリダイレクト
        exit;
    } catch (PDOException $e) {
        exit("エラー: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container py-5 mt-5 bg-success text-white">
        <h1>商品編集</h1>
        <form action="<?php echo htmlspecialchars(BASE_PATH . 'edit_product.php?id=' . $_GET['id']); ?>" method="post">
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
