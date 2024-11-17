<?php

include('connection.php');

// SQL文を準備
$stmt = $conn->prepare("SELECT * FROM products LIMIT 4");

// エラーチェック: SQL文の準備が成功したか
if (!$stmt) {
    die("SQL statement preparation failed: " . $conn->error);
}

// SQL文を実行
$stmt->execute();

// エラーチェック: SQL文の実行が成功したか
if ($stmt->error) {
    die("SQL execution failed: " . $stmt->error);
}

// 結果を取得
$featured_products = $stmt->get_result();

// 取得した結果が空かどうか確認
if ($featured_products->num_rows === 0) {
    echo "No featured products found.";
}
?>
