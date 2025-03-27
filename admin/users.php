<?php
include_once('../config.php');
session_start();

if ($_SESSION['admin_login'] === false) {
    header("location: " . BASE_PATH . "index.php");  // BASE_PATHを使用
    exit;
}


// 1ページあたりの表示件数を設定
$rows = 10; // 適切な値に設定

// 現在のページを取得（デフォルト値は1）
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// オフセットを計算
$offset = ($page - 1) * $rows;

// 検索処理
$name = isset($_GET['name']) ? '%' . htmlspecialchars($_GET['name'], ENT_QUOTES, 'utf-8') . '%' : '';

// 全件数取得
$sql = $name ? "SELECT COUNT(*) FROM users WHERE name LIKE :name" : "SELECT COUNT(*) FROM users";
$stmt = $conn->prepare($sql);
if ($name) {
    $stmt->bindParam(":name", $name);
}

$stmt->execute();
$all_rows = $stmt->fetchColumn();

// ページ数計算
$pages = ceil($all_rows / $rows);
$next = ($page < $pages) ? $page + 1 : null;
$prev = ($page > 1) ? $page - 1 : null;

// ユーザー一覧取得
$sql = $name ? "SELECT * FROM users WHERE name LIKE :name LIMIT :offset, :rows" : "SELECT * FROM users LIMIT :offset, :rows";
$stmt = $conn->prepare($sql);
if ($name) {
    $stmt->bindParam(":name", $name);
}

$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
$stmt->bindParam(":rows", $rows, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員管理</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1><a href="<?php echo BASE_PATH; ?>dashboard.php">管理画面</a></h1>
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
                    <h3>会員管理</h3>
                </div>
                <form class="serch" action="users.php" method="GET">
                    <input type="text" name="name" placeholder="名前検索">
                    <button type="submit" class="btn btn-blue">検索</button>
                </form>
                <div class="list">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>名前</th>
                                <th>メールアドレス</th>
                                <th>送信</th>
                                <th>削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php echo $user['email_verified_at'] ? '受信済み' : '未送信'; ?>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_PATH; ?>admin/delete_product.php?id=<?php echo $user['id']; ?>" class="btn btn-red" onclick="return confirm('本当に削除しますか？');">削除</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <ul class="paging">
                        <li><a href="./users.php?name=<?php echo urlencode($_GET['name'] ?? ''); ?>">« 最初</a></li>
                        <?php if ($prev): ?>
                            <li><a href="./users.php?page=<?php echo $prev; ?>&name=<?php echo urlencode($_GET['name'] ?? ''); ?>"><?php echo $page - 1; ?></a></li>
                        <?php endif; ?>
                        <li><span><?php echo $page; ?></span></li>
                        <?php if ($next): ?>
                            <li><a href="./users.php?page=<?php echo $next; ?>&name=<?php echo urlencode($_GET['name'] ?? ''); ?>"><?php echo $page + 1; ?></a></li>
                        <?php endif; ?>
                        <li><a href="./users.php?page=<?php echo $pages; ?>&name=<?php echo urlencode($_GET['name'] ?? ''); ?>">最後 »</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="container">
            <p>Copyright &copy; 2018 SQUARE, inc</p>
        </div>
    </footer>
</body>
</html>
