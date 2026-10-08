<?php

session_start();

if (!isset($_SESSION['order_number'])) {
    header('Location: index.php');
    exit;
}

$orderNumber = $_SESSION['order_number'];
unset($_SESSION['order_number']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container">
    <div class="success">
        <div class="success-icon">✓</div>

        <h1>Order Placed Successfully</h1>

        <p>Thank you for your order.</p>

        <p>Your order number is:</p>

        <h2><?= htmlspecialchars($orderNumber) ?></h2>

        <a href="index.php" class="add-cart">Back to Product</a>
    </div>
</div>

</body>
</html>