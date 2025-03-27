<?php
session_start();  

include_once('../config.server.php');

// ログイン済みの場合はダッシュボードにリダイレクト
if (isset($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {
    header("Location: dashboard.php");
    exit;
}

// POSTリクエストであるか確認
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームから送信されたデータを受け取る
    $email = $_POST['email'];
    $password = md5($_POST['password']);  // パスワードをMD5で暗号化

    try {
        
        // 入力されたメールアドレスを使ってユーザーを検索
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND password = :password");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ユーザーが存在する場合
        if ($user) {
            $_SESSION['admin_login'] = true;  // ログイン成功
            header("Location: dashboard.php");
            exit;
        } else {
            // ログイン失敗
            header("Location: index.php?error=invalid_password");
            exit;
        }
    } catch (PDOException $e) {
        die("DB接続エラー: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-light">
<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow-sm" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4">管理者ログイン</h3>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">メールアドレス</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="メールアドレスを入力" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">パスワード</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="パスワードを入力" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">ログイン</button>
            </div>
        </form>
    </div>
</div>

<footer class="text-center mt-4">
    <p>&copy; 2024 Your Company, Inc</p>
</footer>
</body>
</html>
