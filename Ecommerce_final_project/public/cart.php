<?php
// Shopping cart page

 require_once __DIR__ . '/../config/database.php';

 require_once __DIR__ . '/../src/controllers/CartController.php';
 require_once __DIR__ . '/../src/helpers/Session.php';
 require_once __DIR__ . '/../src/helpers/Auth.php';

 Session::start();

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $db = getDatabaseConnection();
     $cartController = new CartController($db);

     $action = $_POST['action'] ?? '';

     switch($action) {
         case 'add':
             $cartController->addToCart();
             break;
         case 'update':
             $cartController->updateQuantity();
             break;
         case 'remove':
             $cartController->removeItem();
             break;
         case 'clear':
             $cartController->clearCart();
             break;
     }
 }

 $db = getDatabaseConnection();
 $cartController = new CartController($db);
 $cartController->index();
