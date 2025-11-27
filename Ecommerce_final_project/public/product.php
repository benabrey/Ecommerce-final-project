<?php
// Single product detail page

 require_once __DIR__ . '/../config/database.php';

 require_once __DIR__ . '/../src/controllers/ProductController.php';
 require_once __DIR__ . '/../src/helpers/Session.php';

 Session::start();

 $db = getDatabaseConnection();

 $productController = new ProductController($db);

 $productId = $_GET['id'] ?? null;

 if (!$productId || !is_numeric($productId)) {
     Session::flash('error', 'Invalid product ID');
     header('Location: /products.php');
     exit();
 }

 $productController->show((int)$productId);