<!-- Shopping Cart Page -->
<?php
/** @var array $cart */
/** @var float $total */

$cart = $cart ?? [];
$total = $total ?? 0;
?>

<main class="cart-page">
    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>

        <?php if (!empty($cart)): ?>
            <div class="cart-content">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cart as $item): ?>
                            <tr class="cart-item">
                                <td class="product-info">
                                    <div class="product-display">
                                        <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>"
                                             alt="<?php echo htmlspecialchars($item['product']['name']); ?>"
                                             class="product-thumbnail"
                                             onerror="this.src='/assets/images/placeholder.jpg'">
                                        <div class="product-details">
                                            <h3>
                                                <a href="/public/products.php?id=<?php echo $item['product']['id']; ?>">
                                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                                </a>
                                            </h3>
                                            <p class="product-category">
                                                <?php echo htmlspecialchars($item['product']['category']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="product-price">
                                    $<?php echo number_format($item['product']['price'], 2); ?>
                                </td>

                                <td class="product-quantity">
                                    <form action="/public/cart.php" method="POST" class="quantity-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <div class="quantity-control">
                                            <button type="button" class="qty-btn qty-minus">-</button>
                                            <label>
                                                <input type="number"
                                                       name="quantity"
                                                       value="<?php echo $item['quantity']; ?>"
                                                       min="1"
                                                       max="<?php echo $item['product']['stock_quantity']; ?>"
                                                       class="qty-input">
                                            </label>
                                            <button type="button" class="qty-btn qty-plus">+</button>
                                        </div>
                                        <button type="submit" class="btn-update">Update</button>
                                    </form>
                                </td>

                                <td class="product-subtotal">
                                    <strong>$<?php echo number_format($item['subtotal'], 2); ?></strong>
                                </td>

                                <td class="product-actions">
                                    <form action="/public/cart.php" method="POST" class="remove-form">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <button type="submit" class="btn-remove" onclick="return confirm('Remove this item from cart?')">
                                            <i class="fa fa-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <h2>Cart Summary</h2>

                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>Calculated at checkout</span>
                    </div>

                    <div class="summary-row">
                        <span>Tax:</span>
                        <span>Calculated at checkout</span>
                    </div>

                    <hr>

                    <div class="summary-row total">
                        <strong>Total:</strong>
                        <strong>$<?php echo number_format($total, 2); ?></strong>
                    </div>

                    <a href="/public/checkout.php" class="btn btn-primary btn-block btn-lg">
                        Proceed to Checkout
                    </a>

                    <a href="/public/products.php" class="btn btn-outline btn-block">
                        Continue Shopping
                    </a>

                    <form action="/public/cart.php" method="POST" class="clear-cart-form">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn-link" onclick="return confirm('Clear entire cart?')">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <i class="fa fa-shopping-cart fa-5x"></i>
                <h2>Your cart is empty</h2>
                <p>Add some products to get started!</p>
                <a href="/public/products.php" class="btn btn-primary btn-lg">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.qty-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                if (input.value > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });

        document.querySelectorAll('.qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                const max = parseInt(input.getAttribute('max'));
                if (input.value < max) {
                    input.value = parseInt(input.value) + 1;
                }
            });
        });
    });
</script>
