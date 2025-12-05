<?php
// Products listing page

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/controllers/ProductController.php';
require_once __DIR__ . '/../src/helpers/Session.php';

Session::start();

$db = getDatabaseConnection();
$productController = new ProductController($db);

// Set page title
$pageTitle = "Products - ShopHub";

// Show products listing
$productController->index();
