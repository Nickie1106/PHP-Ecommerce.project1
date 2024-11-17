<?php

session_start();

if ($_SESSION['admin_login'] !== true) {
    header("Location: index.php");
    exit;
}


// 値受け取り
$product_name = htmlspecialchars($_POST['product_name'], ENT_QUOTES, 'UTF-8');
$text = htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8');
$text = nl2br($text);
$price = intval($_POST['price']);

// 画像ファイルのアップロード
if (is_uploaded_file($_FILES['img']['tmp_name'])) {
    $file_name = date('YmdHis') . "_" . $_FILES['img']['name'];
    $file_tmp_name = $_FILES['img']['tmp_name'];
    $upload_dir = "./products/";

// 画像を指定フォルダに保存
if (move_uploaded_file($file_tmp_name, "./products/" . $file_name)) {
    // アップロード完了
    // DB接続
    try {
        $dbh = new PDO("mysql:host=127.0.0.1;dbname=nishimura_php_project", "nishimura", "nishimura");
    } catch (PDOException $e) {
        var_dump($e->getMessage());
        exit;
    }

    // データベースへ登録
    $stmt = $dbh->prepare("INSERT INTO products(
        product_name,
        product_description,
        product_price,
        product_image,
        created_at,
        updated_at
    ) VALUES(
        :product_name,
        :text,
        :price,
        :img_path,
        now(),
        now()
    )");

    // パラメータをバインド
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':text', $text);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':img_path', $file_name);

    // SQLを実行
    if ($stmt->execute()) {
        echo "登録完了";
    } else {
        echo "データベースへの登録に失敗しました。";
    }
} else {
    echo "画像をアップロードできません。";
    exit;
}


    // jpg, png形式のみ許可
    if (pathinfo($file_name, PATHINFO_EXTENSION) == 'jpg' || pathinfo($file_name, PATHINFO_EXTENSION) == 'png') {
        if (move_uploaded_file($file_tmp_name, $upload_dir . $file_name)) {
            try {
                $dbh = new PDO("mysql:host=localhost;dbname=nishimura_php_project", "root", "");
                $stmt = $dbh->prepare("INSERT INTO products (product_name, text, price, img_path, created_at, updated_at) 
                                       VALUES (:product_name, :text, :price, :img_path, NOW(), NOW())");
                $stmt->bindParam(':product_name', $product_name);
                $stmt->bindParam(':text', $text);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':img_path', $file_name);
                $stmt->execute();

                header("Location: products.php");
                exit;
            } catch (PDOException $e) {
                echo "DB接続エラー：" . $e->getMessage();
            }
        } else {
            echo "画像をアップロードできません。";
        }
    } else {
        echo "ファイル形式はjpg/pngのみです";
    }
}
?>
