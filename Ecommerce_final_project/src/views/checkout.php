<!-- Checkout Page -->
<?php
/** @var array $cartItems*/
/** @var float $total*/
/** @var array $user*/

$shippingCost = ($total >= 50) ? 0.00 : 10.00;
?>

<main class="checkout-page">
    <div class="container">
        <h1 class="page-title">Checkout</h1>

        <div class="checkout-content">
            <div class="checkout-form-section">
                <form action="/public/checkout.php" method="POST" id="checkout-form">

                    <div class="form-section">
                        <h2>Shipping Information</h2>

                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   placeholder="(555) 123-4567">
                        </div>

                        <div class="form-group">
                            <label for="shipping_address">Street Address *</label>
                            <input type="text"
                                   id="shipping_address"
                                   name="shipping_address"
                                   placeholder="123 Main Street"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="shipping_city">City *</label>
                            <input type="text"
                                   id="shipping_city"
                                   name="shipping_city"
                                   required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="shipping_postal_code">Postal Code *</label>
                                <input type="text"
                                       id="shipping_postal_code"
                                       name="shipping_postal_code"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="shipping_country">Country *</label>
                                <select id="shipping_country" name="shipping_country" required>
                                    <option value="">Select Country</option>
                                    <option value="USA">United States</option>
                                    <option value="Canada">Canada</option>
                                    <option value="UK">United Kingdom</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Section -->
                    <div class="form-section">
                        <h2>Payment Information</h2>

                        <div class="alert alert-info">
                            <strong>📝 Test Mode:</strong> This is a demo store. Use the test card below:
                            <br><strong>Card:</strong> 4242 4242 4242 4242
                            <br><strong>Expiry:</strong> Any future date
                            <br><strong>CVV:</strong> Any 3 digits
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Payment Method *</label>
                            <select id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="test_card">Test Credit Card (4242 4242 4242 4242)</option>
                                <option value="cash_on_delivery">Cash on Delivery</option>
                            </select>
                        </div>

                        <div id="card-details" class="payment-details">
                            <div class="form-group">
                                <label for="card_number">Card Number</label>
                                <input type="text"
                                       id="card_number"
                                       name="card_number"
                                       placeholder="4242 4242 4242 4242"
                                       value="4242424242424242"
                                       maxlength="19">
                                <small>Use test card: 4242 4242 4242 4242</small>
                            </div>

                            <div class="form-group">
                                <label for="card_name">Cardholder Name</label>
                                <input type="text"
                                       id="card_name"
                                       name="card_name"
                                       placeholder="Test User">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="text"
                                           id="expiry_date"
                                           name="expiry_date"
                                           placeholder="12/25"
                                           value="12/25"
                                           maxlength="5">
                                </div>

                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text"
                                           id="cvv"
                                           name="cvv"
                                           placeholder="123"
                                           value="123"
                                           maxlength="4">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Order Notes (Optional)</h2>
                        <div class="form-group">
                            <label for="order_notes">Special instructions or delivery notes</label>
                            <textarea id="order_notes"
                                      name="order_notes"
                                      rows="4"
                                      placeholder="Any special instructions for your order..."></textarea>
                        </div>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="agree_terms" required>
                            I agree to the <a href="/public/terms.php" target="_blank">Terms and Conditions</a> *
                        </label>
                    </div>

                    <div class="form-actions">
                        <a href="/public/cart.php" class="btn btn-outline">Back to Cart</a>
                        <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>

                <div class="summary-items">
                    <?php foreach ($cartItems as $item): ?>
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
                                $<?php echo number_format($item['subtotal'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr>

                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>

                <div class="summary-row">
                    <span>Shipping:</span>
                    <span id="shipping-cost">$<?php echo number_format($shippingCost,2); ?></span>
                </div>

                <div class="summary-row">
                    <span>Tax:</span>
                    <span id="tax-amount">$<?php echo number_format($total * 0.12, 2); ?></span>
                </div>

                <hr>

                <div class="summary-row total">
                    <strong>Total:</strong>
                    <strong id="order-total">$<?php echo number_format($total * 1.12 + $shippingCost, 2); ?></strong>
                </div>

                <div class="security-badges">
                    <p><i class="fa fa-lock"></i> Secure Checkout</p>
                    <p><i class="fa fa-shield-alt"></i> Your information is protected</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethod = document.getElementById('payment_method');
        const cardDetails = document.getElementById('card-details');
        const cardNumberInput = document.getElementById('card_number');
        const expiryInput = document.getElementById('expiry_date');
        const cvvInput = document.getElementById('cvv');
        const form = document.getElementById('checkout-form');

        paymentMethod.addEventListener('change', function() {
            if (this.value === 'test_card') {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        });

        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // remove non-digits
            value = value.match(/.{1,4}/g)?.join(' ') || '';
            this.value = value;
        });

        expiryInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // remove non-digits
            if (value.length > 2) {
                value = value.substring(0,2) + '/' + value.substring(2,4);
            }
            this.value = value;
        });

        cvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').substring(0,4);
        });

        form.addEventListener('submit', function(e) {
            if (paymentMethod.value === 'test_card') {
                if (cardNumberInput.value.replace(/\s/g,'') !== '4242424242424242') {
                    alert('Use test card: 4242 4242 4242 4242');
                    e.preventDefault();
                }
            }
        });
    });
</script>
