<?php
session_start();
include('server/connection.php');

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $order_status = isset($_GET['order_status']) ? $_GET['order_status'] : '';

    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $order_details = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        die("クエリの実行に失敗しました: ". $stmt->error);
    }

    $order_total_price = calculateTotalOrderPrice($order_details);
    $stmt->close();
} else {
    header('location: account.php');
    exit();
}

function calculateTotalOrderPrice($order_details) {
    $total = 0;
    foreach ($order_details as $row) {
        $total += $row['product_price'] * $row['product_quantity'];
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" crossorigin="anonymous"/>
    <link rel="stylesheet" href="/layouts/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top">
    <div class="container">
        <h5>8</h5>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact us</a></li>
                <li class="nav-item">
                    <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
                    <a href="account.php"><i class="fas fa-user"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!--注文詳細-->
<section id="orders" class="my-5 py-5">
    <div class="container">
        <div class="text-center mt-3 pt-5">
            <h2 class="font-weight-bold">Order Details</h2>
            <hr class="mx-auto" style="width: 100px;">
        </div>

        <div class="table-responsive mx-auto" style="max-width: 800px;">
            <table class="table orders-table text-center mx-auto">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($order_details)) { ?>
                        <?php foreach ($order_details as $row) { ?>
                        <tr>
                            <td>
                                <div class="product-info d-flex align-items-center">
                                    <img src="/layouts/assets/img/<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" class="img-fluid" style="max-width: 80px; margin-right: 15px;">
                                    <p class="mb-0"><?php echo htmlspecialchars($row['product_name']); ?></p>
                                </div>
                            </td>
                            <td><span>$<?php echo htmlspecialchars($row['product_price']); ?></span></td>
                            <td><span><?php echo htmlspecialchars($row['product_quantity']); ?></span></td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3">No order details found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-3">
            <form method="POST" action="payment.php" style="display: inline-block;">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
                <input type="hidden" name="order_status" value="<?php echo htmlspecialchars($order_status); ?>">
                <input type="submit" name="order_details_btn" class="btn btn-primary" value="Pay now">
            </form>
        </div>
    </div>
</section>

<?php include('layouts/footer.php'); ?>

</body>
</html>
