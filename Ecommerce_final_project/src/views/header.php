<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!class_exists('Auth')) {
    require_once __DIR__ . '/../helpers/Auth.php';
}
if (!class_exists('Session')) {
    require_once __DIR__ . '/../helpers/Session.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Dynamic page title -->
    <title><?php echo $pageTitle ?? 'E-Commerce Store'; ?></title>

    <!-- Meta description for SEO -->
    <meta name="description" content="<?php echo $pageDescription ?? 'Your one-stop shop for amazing products at great prices'; ?>">

    <!-- Main CSS -->
    <link rel="stylesheet" href="/public/assets/css/style.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="wrapper">