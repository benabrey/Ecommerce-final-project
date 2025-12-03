<?php
// Checkout page

 require_once __DIR__ . '/../config/database.php';

 require_once __DIR__ . '/../src/controllers/CartController.php';
 require_once __DIR__ . '/../src/helpers/Session.php';
 require_once __DIR__ . '/../src/helpers/Auth.php';

 Session::start();

 Auth::requireAuth();

 $db = getDatabaseConnection();

 $cartController = new CartController($db);

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $cartController->processCheckout();
 } else {
     $cartController->showCheckout();
 }
