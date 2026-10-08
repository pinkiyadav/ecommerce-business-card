<?php

session_start();

require_once 'config/database.php';
require_once 'classes/Pricing.php';
require_once 'classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$size = trim($_POST['size'] ?? '');
$paperType = trim($_POST['paper_type'] ?? '');

if (!$productId || !$quantity || empty($size) || empty($paperType)) {
    $_SESSION['error'] = 'Please complete all product options.';
    header('Location: index.php');
    exit;
}

$allowedQuantities = [100, 500, 1000];
$allowedPaperTypes = ['Matte', 'Glossy'];
$allowedSizes = ['Standard', 'Square'];

if (!in_array($quantity, $allowedQuantities, true)) {
    $_SESSION['error'] = 'Invalid quantity selected.';
    header('Location: index.php');
    exit;
}

if (!in_array($paperType, $allowedPaperTypes, true)) {
    $_SESSION['error'] = 'Invalid paper type selected.';
    header('Location: index.php');
    exit;
}

if (!in_array($size, $allowedSizes, true)) {
    $_SESSION['error'] = 'Invalid size selected.';
    header('Location: index.php');
    exit;
}

if (!isset($_FILES['artwork']) || $_FILES['artwork']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Please upload your artwork.';
    header('Location: index.php');
    exit;
}

$file = $_FILES['artwork'];
$maxFileSize = 5 * 1024 * 1024; // 5 MB

if ($file['size'] > $maxFileSize) {
    $_SESSION['error'] = 'Artwork file must be less than 5 MB.';
    header('Location: index.php');
    exit;
}


$allowedMimeTypes = [
    'image/jpeg',
    'image/png',
    'application/pdf'
];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $allowedMimeTypes, true)) {
    $_SESSION['error'] = 'Invalid artwork format. Please upload JPG, PNG or PDF.';
    header('Location: index.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$productModel = new Product($db);
$pricing = new Pricing($db);

$product = $productModel->getProduct($productId);

if (!$product) {
    $_SESSION['error'] = 'Product not found.';
    header('Location: index.php');
    exit;
}

$price = $pricing->calculatePrice($productId, $quantity, $paperType);

if ($price === null) {
    $_SESSION['error'] = 'Price is not available for this configuration.';
    header('Location: index.php');
    exit;
}

$extensionMap = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'application/pdf' => 'pdf'
];

$extension = $extensionMap[$mimeType];
$storedFileName = bin2hex(random_bytes(16)) . '.' . $extension;

$uploadDirectory = __DIR__ . '/uploads/artwork/';

if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0755, true);
}

$filePath = $uploadDirectory . $storedFileName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    $_SESSION['error'] = 'Unable to upload artwork. Please try again.';
    header('Location: index.php');
    exit;
}

$_SESSION['cart'] = [
    'product_id' => $productId,
    'product_name' => $product['name'],
    'size' => $size,
    'paper_type' => $paperType,
    'quantity' => $quantity,
    'price' => $price,
    'artwork' => [
        'original_name' => $file['name'],
        'stored_name' => $storedFileName,
        'file_path' => 'uploads/artwork/' . $storedFileName,
        'mime_type' => $mimeType,
        'file_size' => $file['size']
    ]
];

header('Location: cart.php');
exit;