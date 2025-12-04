<!-- Product Detail Page -->
<?php
$product = $product ?? null;
$relatedProducts = $relatedProducts ?? [];
?>

<main class="product-detail-page">
    <div class="container">

        <nav class="breadcrumb">
            <a href="/public/index.php">Home</a>
            <span>/</span>
            <a href="/public/products.php">Products</a>
            <span>/</span>
            <a href="/public/products.php?category=<?php echo urlencode($product['category']); ?>">
                <?php echo htmlspecialchars($product['category']); ?>
            </a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </nav>

        <div class="product-detail-content">
            <div class="product-images">
                <div class="main-image">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         id="main-product-image"
                         onerror="this.src='/assets/images/placeholder.jpg'">
                </div>

                <div class="image-thumbnails">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                         alt="Thumbnail"
                         class="thumbnail active"
                         onclick="changeMainImage(this.src)">
                </div>
            </div>

            <div class="product-info">
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="product-rating">
                    <div class="stars">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star-half-alt"></i>
                    </div>
                    <span class="rating-count">(4.5 out of 5 - 123 reviews)</span>
                </div>

                <div class="product-price">
                    <span class="price-amount">$<?php echo number_format($product['price'], 2); ?></span>
                    <!-- <span class="price-original">$XX.XX</span> -->
                    <!-- <span class="price-discount">20% OFF</span> -->
                </div>

                <div class="stock-info">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <span class="stock-available">
                            <i class="fa fa-check-circle"></i>
                            In Stock (<?php echo $product['stock_quantity']; ?> available)
                        </span>
                    <?php else: ?>
                        <span class="stock-unavailable">
                            <i class="fa fa-times-circle"></i>
                            Out of Stock
                        </span>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="product-specs">
                    <h3>Specifications</h3>
                    <ul>
                        <li><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></li>
                        <li><strong>SKU:</strong> <?php echo htmlspecialchars($product['id']); ?></li>
                    </ul>
                </div>

                <?php if ($product['stock_quantity'] > 0): ?>
                    <form action="/public/cart.php" method="POST" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <div class="quantity-control">
                                <button type="button" class="qty-btn qty-minus">-</button>
                                <input type="number"
                                       id="quantity"
                                       name="quantity"
                                       value="1"
                                       min="1"
                                       max="<?php echo $product['stock_quantity']; ?>"
                                       class="qty-input">
                                <button type="button" class="qty-btn qty-plus">+</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-add-to-cart">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg" disabled>
                        Out of Stock
                    </button>
                    <button class="btn btn-outline">
                        <i class="fa fa-bell"></i> Notify Me When Available
                    </button>
                <?php endif; ?>

                <div class="product-actions">
                    <button class="btn-wishlist" title="Add to Wishlist">
                        <i class="fa fa-heart"></i> Add to Wishlist
                    </button>
                    <button class="btn-share" title="Share">
                        <i class="fa fa-share-alt"></i> Share
                    </button>
                </div>
            </div>
        </div>

        <div class="product-tabs">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="reviews">Reviews</button>
                <button class="tab-btn" data-tab="shipping">Shipping Info</button>
                <button class="tab-btn" data-tab="returns">Returns</button>
            </div>

            <div class="tabs-content">
                <div class="tab-pane active" id="reviews">
                    <h3>Customer Reviews</h3>
                    <p>No reviews yet. Be the first to review this product!</p>
                    <button class="btn btn-outline">Write a Review</button>
                </div>

                <div class="tab-pane" id="shipping">
                    <h3>Shipping Information</h3>
                    <p>Free shipping on orders over $50. Standard shipping takes 3-5 business days.</p>
                </div>

                <div class="tab-pane" id="returns">
                    <h3>Return Policy</h3>
                    <p>We offer a 30-day return policy. Items must be in original condition.</p>
                </div>
            </div>
        </div>

        <?php if (!empty($relatedProducts)): ?>
            <section class="related-products">
                <h2 class="section-title">Related Products</h2>
                <div class="product-grid">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <?php if ($relatedProduct['id'] != $product['id']):  ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="/public/products.php?id=<?php echo $relatedProduct['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($relatedProduct['image_url']); ?>"
                                             alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>"
                                             onerror="this.src='/assets/images/placeholder.jpg'">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h3>
                                        <a href="/public/products.php?id=<?php echo $relatedProduct['id']; ?>">
                                            <?php echo htmlspecialchars($relatedProduct['name']); ?>
                                        </a>
                                    </h3>
                                    <div class="product-price">
                                        $<?php echo number_format($relatedProduct['price'], 2); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyMinus = document.querySelector('.qty-minus');
        const qtyPlus = document.querySelector('.qty-plus');
        const qtyInput = document.querySelector('.qty-input');

        if (qtyMinus && qtyPlus && qtyInput) {
            qtyMinus.addEventListener('click', function() {
                if (qtyInput.value > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                }
            });

            qtyPlus.addEventListener('click', function() {
                const max = parseInt(qtyInput.getAttribute('max'));
                if (qtyInput.value < max) {
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                }
            });
        }

        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });

        function changeMainImage(src) {
            document.getElementById('main-product-image').src = src;

            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    });
</script>
