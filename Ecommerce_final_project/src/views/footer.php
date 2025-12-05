</div> <!-- Close wrapper -->

<footer class="footer">
    <div class="container">

        <div class="footer-content">

            <!-- About -->
            <div class="footer-column">
                <h3>About ShopHub</h3>
                <p>Your trusted e-commerce store for quality products at great prices.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="/public/index.php">Home</a></li>
                    <li><a href="/public/products.php">Products</a></li>
                    <li><a href="/public/cart.php">Shopping Cart</a></li>
                    <li><a href="/public/checkout.php">Checkout</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="footer-column">
                <h3>Support</h3>
                <p>Need help? We're here for you.</p>
                <p><strong>Email:</strong> support@shophub.com</p>
                <p><strong>Phone:</strong> (555) 123-4567</p>
            </div>

            <!-- Demo Notice -->
            <div class="footer-column">
                <h3>Demo Information</h3>
                <div class="demo-info">
                    <p><i class="fas fa-info-circle"></i> This is a demonstration e-commerce site.</p>
                    <p><strong>Test Card:</strong> 4242 4242 4242 4242</p>
                    <p><strong>Expiry:</strong> Any future date</p>
                    <p><strong>CVV:</strong> Any 3 digits</p>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ShopHub. All rights reserved.</p>
        </div>

    </div>
</footer>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- JavaScript -->
<script src="/public/assets/js/main.js"></script>

<script>
    // Close alerts
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });

    // Auto-hide after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => alert.remove());
    }, 5000);
</script>
</body>
</html>