<?php

session_start(); // セッションを開始

// セッションをクリア
session_unset();
session_destroy();

// config.php をインクルード
include_once('../config.php');

// 絶対パスを使用してリダイレクト
header("Location: " . BASE_PATH . "index.php");
exit;

?>
