<?php

session_start();

if (!isset($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$cart = $_SESSION['cart'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<?php require_once 'inc/header.php'; ?>
<div class="container">
    <div class="checkout">

        <h1>Checkout</h1>

        <div class="checkout-summary">
            <h3><?= htmlspecialchars($cart['product_name']) ?></h3>

            <p>
                <?= htmlspecialchars($cart['size']) ?> |
                <?= htmlspecialchars($cart['paper_type']) ?> |
                <?= htmlspecialchars($cart['quantity']) ?> cards
            </p>

            <strong>$<?= number_format($cart['price'], 2) ?></strong>
        </div>

        <form method="POST" action="place-order.php">

            <div class="form-group">
                <label for="customer_name">Full Name</label>
                <input type="text" id="customer_name" name="customer_name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea id="address" name="address" rows="5" required></textarea>
            </div>

            <button type="submit" class="add-cart">Place Order</button>

        </form>

    </div>
</div>

</body>
</html>