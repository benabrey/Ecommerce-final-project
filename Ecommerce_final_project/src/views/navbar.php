<nav class="navbar">
    <div class="container">
        <div class="navbar-content">

            <!-- Logo -->
            <div class="navbar-brand">
                <a href="/public/index.php">
                    <i class="fas fa-store"></i>
                    <span>ShopHub</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <ul class="navbar-nav">
                <li><a href="/public/index.php" class="nav-link">Home</a></li>
                <li><a href="/public/products.php" class="nav-link">Products</a></li>

                <!-- Categories Dropdown -->
                <?php if (isset($categories) && !empty($categories)): ?>
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            Categories <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="/public/products.php?category=<?php echo urlencode($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Search Bar -->
            <div class="navbar-search">
                <form action="/public/products.php" method="GET" class="search-form">
                    <label>
                        <input
                                type="text"
                                name="search"
                                placeholder="Search products..."
                                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                class="search-input"
                        >
                    </label>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- User Actions -->
            <div class="navbar-actions">

                <!-- Cart -->
                <a href="/public/cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">
                        <?php
                        $cart = Session::get('cart', []);
                        echo array_sum($cart);
                        ?>
                    </span>
                </a>

                <?php if (Auth::check()): ?>
                    <!-- Logged In Menu -->
                    <div class="user-menu dropdown">
                        <a href="#" class="nav-link dropdown-toggle user-toggle">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars(Auth::user()['username'] ?? 'Account'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="dropdown-header">
                                <strong><?php echo htmlspecialchars(Auth::user()['username']); ?></strong>
                                <small><?php echo htmlspecialchars(Auth::user()['email']); ?></small>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li><a href="#"><i class="fas fa-user"></i> My Account</a></li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <form action="/public/index.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="logout">
                                    <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; width: 100%; text-align: left; padding: 0.5rem 1rem;">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Guest - Show simple text for now -->
                    <span class="nav-link">Guest</span>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>

<!-- Flash Messages -->
<div class="flash-messages">
    <?php if (Session::has('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars(Session::getFlash('success')); ?></span>
            <button class="alert-close">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (Session::has('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo htmlspecialchars(Session::getFlash('error')); ?></span>
            <button class="alert-close">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (Session::has('errors')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Please fix the following errors:</strong>
                <ul class="error-list">
                    <?php foreach (Session::get('errors') as $field => $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button class="alert-close">&times;</button>
        </div>
        <?php Session::remove('errors'); ?>
    <?php endif; ?>
</div>