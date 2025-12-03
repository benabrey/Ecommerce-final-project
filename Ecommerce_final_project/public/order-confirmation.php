<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/controllers/CartController.php';
require_once __DIR__ . '/../src/helpers/Session.php';
require_once __DIR__ . '/../src/helpers/Auth.php';

Session::start();
Auth::requireAuth();

$db = getDatabaseConnection();
$cartController = new CartController($db);

$orderId = $_GET['id'] ?? null;
if (!$orderId || !is_numeric($orderId)) {
    header('Location: /index.php');
    exit();
}

$cartController->showOrderConfirmation($orderId);