<!-- Products Listing Page -->
<main>
    <div class="container">
        <h1>All Products</h1>

        <?php if (isset($products) && !empty($products)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <?php foreach ($products as $product): ?>
                    <div style="background: white; border-radius: 0.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">

                        <a href="/public/product.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="width: 100%; height: 200px; object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                        </a>

                        <div style="padding: 1rem;">
                            <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">
                                <a href="/public/product.php?id=<?php echo $product['id']; ?>" style="color: #1f2937;">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>

                            <p style="color: #6b7280; font-size: 0.875rem;">
                                <?php echo htmlspecialchars($product['category']); ?>
                            </p>

                            <div style="font-size: 1.5rem; font-weight: bold; color: #3b82f6;">
                                $<?php echo number_format($product['price'], 2); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-top: 3rem;">No products found.</p>
        <?php endif; ?>
    </div>
</main>