<?php

session_start();

$servername = "localhost";
$username = "nishimura";
$password = "nishimura";
$dbname = "nishimura_php_project";

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続確認
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POSTデータを取得し、エスケープ処理
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '';
$password = isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') : '';

// 入力チェック
if (empty($email) || empty($password)) {
    // メールアドレスまたはパスワードが空の場合
    header("Location: ./index.php?error=empty_fields");
    exit;
}

// データベースからユーザー情報を取得
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ユーザーが見つかった場合
    $user = $result->fetch_assoc();

    // パスワード確認 (password_hashとpassword_verifyを使用)
    if (password_verify($password, $user['password'])) {
        // パスワードが一致した場合
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in'] = true;

        // ダッシュボードにリダイレクト
        header("Location: ./dashboard.php");
    } else {
        // パスワードが間違っている場合
        header("Location: ./index.php?error=invalid_password");
        exit;
    }
} else {
    // ユーザーが見つからない場合
    header("Location: ./index.php?error=invalid_email");
    exit;
}

$stmt->close();
$conn->close();

?>
