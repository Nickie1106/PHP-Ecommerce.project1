<?php

include_once('../config.php');
    session_start();
    if ($_SESSION['admin_login'] == false) {
        header("Location:./server/index.php");
        exit;
    }
?>




<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>


<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1><a href="<?php echo BASE_URL; ?>dashboard.php">管理画面</a></h1>
            </div>
            <nav class="menu-right menu">
                <a href="<?php echo BASE_URL; ?>login.php" target="_blank" class="view-site">サイトを表示</a>
                <a href="<?php echo BASE_URL; ?>logout.php">ログアウト</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="wrapper">
            <div class="container">
                <div class="wrapper-title">
                    <h3>ダッシュボード</h3>
                </div>
                <div class="boxs">
                    <a href="<?php echo BASE_URL; ?>admin/users.php" class="box">
                        <i class="fas fa-users icon"></i>
                        <p>会員管理</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/product_list.php" class="box">
                        <i class="fas fa-ambulance icon"></i>
                        <p>商品一覧</p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/products.php" class="box">
                        <i class="fas fa-store-alt icon"></i>
                        <p>商品管理</p>
                    </a>
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
