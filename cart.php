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
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<?php require_once 'inc/header.php'; ?>
<div class="container">
    <div class="cart-container">

        <h1>Shopping Cart</h1>

        <div class="cart-item">

            <div>
                <h2><?= htmlspecialchars($cart['product_name']) ?></h2>

                <p><strong>Size:</strong> <?= htmlspecialchars($cart['size']) ?></p>
                <p><strong>Paper:</strong> <?= htmlspecialchars($cart['paper_type']) ?></p>
                <p><strong>Quantity:</strong> <?= htmlspecialchars($cart['quantity']) ?></p>
                <p><strong>Artwork:</strong> <?= htmlspecialchars($cart['artwork']['original_name']) ?></p>
            </div>

            <div class="cart-price">
                $<?= number_format($cart['price'], 2) ?>
            </div>

        </div>

        <div class="price-box">
            <span>Total</span>
            <strong>$<?= number_format($cart['price'], 2) ?></strong>
        </div>

        <div class="cart-actions">
            <a href="index.php" class="continue-button">Continue Shopping</a>
            <a href="checkout.php" class="add-cart">Proceed to Checkout</a>
        </div>

    </div>
</div>

</body>
</html>