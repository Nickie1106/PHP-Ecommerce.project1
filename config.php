<?php
// 環境に応じたベースURLの設定
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
        define('BASE_URL', '/');
    } else {
        define('BASE_URL', '/project-php/');
    }
}
?>
