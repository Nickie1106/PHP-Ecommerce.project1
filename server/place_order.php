<?php 
session_start();
include('config.php');

if (isset($_POST['place_order'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $order_cost = $_SESSION['total']; // 合計金額を取得
    $order_status = "on hold";
    $user_id = $_SESSION['user_id']; // ログイン中のユーザーID
    $order_date = date('Y-m-d H:i:s');

    try {
        // orders テーブルに挿入
        $query = "INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$order_cost, $order_status, $user_id, $phone, $city, $address, $order_date]);

        // 挿入された order_id を取得
        $order_id = $conn->lastInsertId();

        // カート内の商品を order_items に挿入
        $cart = $_SESSION['cart'];
        $query = "INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, order_date)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        foreach ($cart as $item) {
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['product_name'],
                $item['product_image'],
                $item['product_price'],
                $item['product_quantity'],
                $order_date
            ]);
        }

        // カートをクリア
        $_SESSION['cart'] = [];
        echo "<script>alert('Order placed successfully!'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
