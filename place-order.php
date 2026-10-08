<?php

session_start();

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['error'] = 'Your cart is empty.';
    header('Location: index.php');
    exit;
}

$customerName = trim($_POST['customer_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if (empty($customerName) || empty($email) || empty($phone) || empty($address)) {
    $_SESSION['error'] = 'Please complete all checkout fields.';
    header('Location: checkout.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    header('Location: checkout.php');
    exit;
}

$cart = $_SESSION['cart'];

$database = new Database();
$db = $database->connect();

$orderNumber = 'BC-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

try {
    $db->beginTransaction();
    
    $sql = "INSERT INTO orders (order_number, customer_name, email, phone, address, product_id, size, paper_type, quantity, price, artwork_file, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($sql);

    $stmt->execute([$orderNumber, $customerName, $email, $phone, $address, $cart['product_id'], $cart['size'], $cart['paper_type'], $cart['quantity'], $cart['price'], $cart['artwork']['stored_name'], 'pending']);

    $orderId = $db->lastInsertId();
    
    $artworkSql = "INSERT INTO uploaded_artwork_files (order_id, original_name, stored_name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?, ?)";

    $artworkStmt = $db->prepare($artworkSql);

    $artworkStmt->execute([$orderId, $cart['artwork']['original_name'], $cart['artwork']['stored_name'], $cart['artwork']['file_path'], $cart['artwork']['mime_type'], $cart['artwork']['file_size']]);

    $db->commit();

    unset($_SESSION['cart']);
   
    $_SESSION['order_number'] = $orderNumber;

    header('Location: order-success.php');
    exit;

} catch (Exception $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $_SESSION['error'] = 'Unable to place order. Please try again.';

    header('Location: checkout.php');
    exit;
}