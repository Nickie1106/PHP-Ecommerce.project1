<?php
session_start();

include('../config.php');

if ($_SESSION['admin_login'] !== true) {
    header("Location: index.php");
    exit;
}

// フォームからのデータ受け取り（存在チェック）
if (isset($_POST['product_name'], $_POST['text'], $_POST['price'], $_FILES['img']['tmp_name'])) {
    // 値受け取り
    $product_name = htmlspecialchars($_POST['product_name'], ENT_QUOTES, 'UTF-8');
    $text = htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8');
    $text = nl2br($text);  // 改行タグを変換
    $price = floatval($_POST['price']);  

    // 画像ファイルのアップロード
    $file_names = [];
    $file_fields = ['img'];  // 複数の画像フィールド名を配列で指定
    foreach ($file_fields as $file_field) {
        if (is_uploaded_file($_FILES[$file_field]['tmp_name'])) {
            $file_name = date('YmdHis') . "_" . $_FILES[$file_field]['name'];
            $file_tmp_name = $_FILES[$file_field]['tmp_name'];
            $upload_dir = "./products/";

            // ディレクトリが存在しない場合は作成
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0775, true);  // ディレクトリ作成
            }

            // 画像を指定のディレクトリに移動
            if (move_uploaded_file($file_tmp_name, $upload_dir . $file_name)) {
                $file_names[$file_field] = $file_name;
            } else {
                $file_names[$file_field] = null;
            }
        }
    }


    // データベースへ登録
    $stmt = $conn->prepare("INSERT INTO products(
        product_name,
        product_description,
        product_price,
        product_image,
        product_color,
        product_quantity,
        created_at,
        updated_at
    ) VALUES(
        :product_name,
        :text,
        :price,
        :img1,
        :color,
        :quantity,
        now(),
        now()
    )");

    // パラメータをバインド
    $stmt->bindValue(':product_name', $product_name);
    $stmt->bindValue(':text', $text);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':img1', $file_names['img'] ?? null);
    $stmt->bindValue(':color', $_POST['color'] ?? '');
    $stmt->bindValue(':quantity', $_POST['quantity'] ?? 1);

    // SQLを実行
    if ($stmt->execute()) {
        // 登録完了後、適切にリダイレクト
        header("Location: product_list.php");  // 商品一覧ページにリダイレクト
        exit;
    } else {
        echo "データベースへの登録に失敗しました。";
    }
} else {
    // 必要なフォームデータが送信されていない場合
    echo "商品名、説明文、価格、または画像が未入力です。";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録</title>

    <link rel="icon" href="<?php echo BASE_PATH; ?>favicon.ico">
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
                    <h3>新規商品登録</h3>
                </div>

                <!-- 商品登録フォーム -->
                <form class="edit-form" method="POST" action="<?php echo BASE_PATH; ?>admin/store_product.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <p>商品名</p>
                        <input type="text" name="product_name" required>
                    </div>
                    <div class="form-group">
                        <p>説明文</p>
                        <input type="text" name="text" maxlength="255" required>
                    </div>
                    <div class="form-group">
                        <p>単価</p>
                        <input type="text" name="price" required>
                    </div>
                    <div class="form-group">
                        <p>アイテム画像1</p>
                        <input type="file" name="img" class="imgform" required>
                    </div>
                    <div class="form-group">
                        <p>色</p>
                        <input type="text" name="color">
                    </div>
                    <div class="form-group">
                        <p>在庫数</p>
                        <input type="number" name="quantity" value="1">
                    </div>

                    <!-- 登録ボタン -->
                    <button type="submit" class="btn btn--primary">登録</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>