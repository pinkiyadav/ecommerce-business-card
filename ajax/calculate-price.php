<?php

header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../classes/Pricing.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$productId = filter_input(
    INPUT_POST,
    'product_id',
    FILTER_VALIDATE_INT
);

$quantity = filter_input(
    INPUT_POST,
    'quantity',
    FILTER_VALIDATE_INT
);

$paperType = trim($_POST['paper_type'] ?? '');

if (!$productId || !$quantity || empty($paperType)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product configuration.'
    ]);
    exit;
}

try {

    $database = new Database();
    $db = $database->connect();

    $pricing = new Pricing($db);

    $price = $pricing->calculatePrice(
        $productId,
        $quantity,
        $paperType
    );

    if ($price === null) {
        echo json_encode([
            'success' => false,
            'message' => 'Price not available for this combination.'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'price' => number_format($price, 2)
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Unable to calculate price.'
    ]);
}