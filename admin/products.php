<?php
include_once('../config.php');
session_start();

if ($_SESSION['admin_login'] == false) {
    header("Location: " . BASE_PATH . "index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品追加</title>

    <link rel="icon" href="<?php echo BASE_PATH; ?>favicon.ico">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">   
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                    <h3 class="container pt-5 mt-5 text-center">商品追加</h3>
                </div>
                <div class="text-right mb-3">
                    <a href="<?php echo BASE_PATH; ?>admin/store_product.php" class="container pt-3 mt-5 btn btn-primary">商品追加</a>
                </div>
                <div class="boxs">
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>Copyright &copy; 2018 SQUARE, inc</p>
        </div>
    </footer>

    <!-- Bootstrap JS, Popper.js, and jQuery (for Bootstrap functionality) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
