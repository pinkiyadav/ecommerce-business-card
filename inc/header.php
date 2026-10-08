<?php

$cartCount = isset($_SESSION['cart']) ? 1 : 0;

?>

<header class="bar"><a class="logo" href="index.php" style="color:#fff;text-decoration:none">PrintBox</a>
    <a href="cart.php" style="color:#fff">
        Cart: <?= $cartCount ?>
    </a>
</header>