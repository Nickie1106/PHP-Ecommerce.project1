<?php
session_start(); 

// セッションが無効の場合はログイン画面にリダイレクト
if ($_SESSION['admin_login'] === false) {
    header("location:./index.php");
    exit;
}

// DB接続とユーザー情報取得
if (isset($_GET['id'])) {
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=nishimura_php_project", "nishimura", "nishimura");
        $stmt = $dbh->prepare("SELECT * FROM users WHERE id = :id"); // 修正: user_id → id
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
    }
}

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 修正: UPDATEのSQL文とバインドパラメータを適切に修正
        $stmt = $dbh->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->bindParam(':name', $_POST['user_name']);
        $stmt->bindParam(':email', $_POST['user_email']);
        $stmt->bindParam(':id', $_POST['user_id'], PDO::PARAM_INT); // 修正: user_id → id
        $stmt->execute();
        header("location: edit_users.php");
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
    <title>ユーザー編集</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- 明るい緑の背景を設定 -->
    <div class="container py-5 mt-5 bg-success text-white">
        <h1>ユーザー編集</h1>
        <?php if ($user): ?>
            <form action="edit_users.php" method="post">
                <!-- user_idをhiddenフィールドとして送信 -->
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                <div class="form-group">
                    <label for="user_name">ユーザー名</label>
                    <input type="text" name="user_name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="user_email">メールアドレス</label>
                    <input type="email" name="user_email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">保存</button>
            </form>
        <?php else: ?>
            <p>ユーザー情報が見つかりませんでした。</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
