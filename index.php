<?php

require_once 'config/database.php';
require_once 'classes/Product.php';

$database = new Database();
$db = $database->connect();

$productModel = new Product($db);

$product = $productModel->getProduct(1);

if (!$product) {
    die('Product not found.');
}

$sizes = $productModel->getOptions($product['id'], 'size');

$papers = $productModel->getOptions($product['id'], 'paper');

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($product['name']) ?></title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
<?php require_once 'inc/header.php'; ?>
<div class="container">

    <div class="product">

        <div class="product-image">

            <div class="card-preview">
                BUSINESS<br>
                CARDS
            </div>

        </div>

        <div class="product-details">

            <h1><?= htmlspecialchars($product['name']) ?></h1>

            <p class="description"><?= htmlspecialchars($product['description']) ?></p>

            <form id="productForm" action="add-to-cart.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <div class="form-group">

                    <label for="size">Card Size</label>

                    <select name="size" id="size" required>

                        <?php foreach ($sizes as $size): ?>

                            <option value="<?= htmlspecialchars($size['option_name']) ?>">
                                <?= htmlspecialchars($size['option_name']) ?> - <?= htmlspecialchars($size['option_value']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>
                
                <div class="form-group">

                    <label>Paper Type</label>

                    <div class="radio-group">

                        <?php foreach ($papers as $index => $paper): ?>

                            <label class="radio-option">
                                <input type="radio" name="paper_type" value="<?= htmlspecialchars($paper['option_name']) ?>" <?= $index === 0 ? 'checked' : '' ?>>
                                <?= htmlspecialchars($paper['option_name']) ?>
                            </label>

                        <?php endforeach; ?>

                    </div>

                </div>

                <div class="form-group">

                    <label for="quantity">Quantity</label>

                    <select name="quantity" id="quantity" required>

                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>

                    </select>

                </div>

                <div class="form-group">

                    <label for="artwork">Upload Artwork</label>

                    <input type="file" name="artwork" id="artwork" accept=".jpg,.jpeg,.png,.pdf" required>

                    <small>Accepted: JPG, PNG, PDF</small>

                </div>

                 <div class="price-box">

                    <span>Price</span>

                    <strong>$<span id="price">20.00</span></strong>

                </div>

                <button type="submit" class="add-cart">Add to Cart</button>

            </form>

        </div>

    </div>

</div>

<script src="assets/js/product.js"></script>

</body>

</html>