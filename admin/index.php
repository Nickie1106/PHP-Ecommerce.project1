<?php

session_start();

if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] === false) {
    header("location: login.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面ログイン</title>
    <link rel="icon" href="favicon.ico">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body class="bg-light">

    <!-- Login Wrapper -->
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow-sm" style="max-width: 400px; width: 100%;">
            <h3 class="text-center mb-4">ログイン</h3>

            <!-- Login Form -->
            <form action="dashboard.php" method="POST">
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

    <!-- Bootstrap JS (optional for some Bootstrap components like dropdowns or modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0Y7p9OXW9n1aK8zJ2n6TLZ9J3eyFowCwtlOEGgwTxwWiWiVJ" crossorigin="anonymous"></script>
</body>

</html>
