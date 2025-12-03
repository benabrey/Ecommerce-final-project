<?php
/** @var array $order */
/** @var array $orderItems */
/** @var float $total */
?>

<main class="order-confirmation-page">
    <div class="container">
        <h1>Thank You for Your Order!</h1>
        <p>Your order ID is <strong>#<?php echo htmlspecialchars($order['id']); ?></strong></p>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="summary-items">
                <?php foreach ($orderItems as $item): ?>
                    <div class="summary-item">
                        <div class="item-info">
                            <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>"
                                 alt="<?php echo htmlspecialchars($item['product']['name']); ?>"
                                 onerror="this.src='/assets/images/placeholder.jpg'">
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['product']['name']); ?></h4>
                                <p>Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                        </div>
                        <div class="item-price">
                            $<?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <div class="summary-row">
                <span>Total Paid:</span>
                <strong>$<?php echo number_format($total, 2); ?></strong>
            </div>
        </div>

        <a href="/public/index.php" class="btn btn-primary btn-lg">Back to Home</a>
        <a href="/public/products.php" class="btn btn-outline btn-lg">Continue Shopping</a>
    </div>
</main>