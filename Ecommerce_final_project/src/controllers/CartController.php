<?php
// Cart controller for handling shopping cart operations

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Mailer.php';

class CartController {

    private $db;
    private $productModel;
    private $orderModel;

    public function __construct($dbConnection) {
         $this->db = $dbConnection;

         $this->productModel = new Product($dbConnection);
         $this->orderModel = new Order($dbConnection);

         Session::start();
    }

    /**
     * Show cart page
     */
    public function index() {
         Auth::requireAuth();

         $cartItems = Session::get('cart', []);

         $cart = [];
         $total = 0;
         foreach ($cartItems as $productId => $quantity) {
             $product = $this->productModel->findById($productId);
             if ($product) {
                 $cart[] = [
                     'product' => $product,
                     'quantity' => $quantity,
                     'subtotal' => $product['price'] * $quantity
                 ];
                 $total += $product['price'] * $quantity;
             }
         }

         require_once __DIR__ . '/../views/cart.php';
    }

    /**
     * Add item to cart
     */
    public function addToCart() {
        Auth::requireAuth();

         $productId = $_POST['product_id'] ?? null;
         $quantity = $_POST['quantity'] ?? 1;

         $validator = new Validator();
         $validator->required('product_id', $productId)
                   ->numeric('product_id', $productId)
                   ->required('quantity', $quantity)
                   ->numeric('quantity', $quantity)
                   ->between('quantity', $quantity, 1, 100);

         $product = $this->productModel->findById($productId);
         if (!$product) {
             Session::flash('error', 'Product not found');
             header('Location: /products.php');
             exit();
         }

         if (!$this->productModel->hasStock($productId, $quantity)) {
             Session::flash('error', 'Insufficient stock');
             header('Location: /products.php?id=' . $productId);
             exit();
         }

         $cart = Session::get('cart', []);

         if (isset($cart[$productId])) {
             $cart[$productId] += $quantity;
         } else {
             $cart[$productId] = $quantity;
         }

         Session::set('cart', $cart);

         Session::flash('success', 'Product added to cart');
         header('Location: /cart.php');
         exit();
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity() {
        Auth::requireAuth();
        if($_SERVER['REQUEST_METHOD'] !=='POST'){
            header('Location: /cart.php');
            exit();
        }
         $productId = $_POST['product_id'] ?? null;
         $quantity = $_POST['quantity'] ?? 0;

        $validator = new Validator();
        $validator->required('product_id', $productId)
                  ->numeric('product_id', $productId)
                  ->required('quantity', $quantity)
                  ->numeric('quantity', $quantity);
        if($validator->fails()){
            Session::set('errors', $validator->getErrors());
            header('Location: /cart.php');
            exit();
        }

        $cart = Session::get('cart', []);

         if ($quantity <= 0) {
             unset($cart[$productId]);
         } else {
             if ($this->productModel->hasStock($productId, $quantity)) {
                 $cart[$productId] = $quantity;
             } else {
                 Session::flash('error', 'Insufficient stock');
                 header('Location: /cart.php');
                 exit();
             }
         }

        Session::set('cart', $cart);

         header('Location: /cart.php');
         exit();
    }

    /**
     * Remove item from cart
     */
    public function removeItem() {
        Auth::requireAuth();
        $productId = $_POST['product_id'] ?? $_GET['id'] ?? null;

        if(!$productId || !is_numeric($productId) ){
            Session::flash('error', 'Invalid product ID');
            header('Location: /cart.php');
            exit();
        }

        $cart = Session::get('cart', []);

        unset($cart[$productId]);

        Session::set('cart', $cart);

         Session::flash('success', 'Item removed from cart');
         header('Location: /cart.php');
         exit();
    }

    /**
     * Clear entire cart
     */
    public function clearCart() {
        Auth::requireAuth();

        Session::remove('cart');

         Session::flash('success', 'Cart cleared');
         header('Location: /cart.php');
         exit();
    }

    /**
     * Get cart count (for navbar badge)
     */
    public function getCartCount() {
        $cart = Session::get('cart', []);
        $count = array_sum($cart);

         header('Content-Type: application/json');
         echo json_encode(['count' => $count]);
         exit();
    }

    /**
     * Calculate cart total
     * @return float Cart total
     */
    private function calculateCartTotal() {
        $cart = Session::get('cart', []);

         $total = 0;
         foreach ($cart as $productId => $quantity) {
             $product = $this->productModel->findById($productId);
             if ($product) {
                 $total += $product['price'] * $quantity;
             }
         }
         return $total;
    }

    /**
     * Show checkout page
     */
    public function showCheckout() {
         Auth::requireAuth();
         $cart = Session::get('cart', []);

         if (empty($cart)) {
             Session::flash('error', 'Your cart is empty');
             header('Location: /cart.php');
             exit();
         }

         $cartItems = [];
         $total = 0;
         foreach ($cart as $productId => $quantity) {
             $product = $this->productModel->findById($productId);
             if ($product) {
                 $cartItems[] = [
                     'product' => $product,
                     'quantity' => $quantity,
                     'subtotal' => $product['price'] * $quantity
                 ];
                 $total += $product['price'] * $quantity;
             }
         }

         $userId = Auth::id();
         $userModel = new User($this->db);
         $user = $userModel->findById($userId);

         require_once __DIR__ . '/../views/checkout.php';
    }

    /**
     * Process checkout and create order
     */
    public function processCheckout() {
         Auth::requireAuth();

         if($_SERVER['REQUEST_METHOD'] !== 'POST'){
             header('Location: /checkout.php');
             exit();
         }
         $cart = Session::get('cart', []);

         if (empty($cart)) {
             Session::flash('error', 'Your cart is empty');
             header('Location: /cart.php');
             exit();
         }

         $shippingAddress = $_POST['shipping_address'] ?? '';
         $shippingCity = $_POST['shipping_city'] ?? '';
         $shippingPostalCode = $_POST['shipping_postal_code'] ?? '';
         $shippingCountry = $_POST['shipping_country'] ?? '';

         $validator = new Validator();
         $validator->required('shipping_address', $shippingAddress)
                   ->required('shipping_city', $shippingCity)
                   ->required('shipping_postal_code', $shippingPostalCode)
                   ->required('shipping_country', $shippingCountry);

         if($validator->fails()){
             Session::set('errors', $validator->getErrors());
             header('Location: /checkout.php');
             exit();
         }

        // Test Credit Card Info:
        $paymentMethod = $_POST['payment_method'] ?? '';
        $cardNumber = $_POST['card_number'] ?? '';

        if($paymentMethod === 'test_card'){
            if($cardNumber !== '4242424242424242'&& str_replace(' ', '', $cardNumber) !== '4242424242424242') {
                Session::flash('error', 'Please use test card: 4242 4242 4242 4242');
                header('Location: /checkout.php');
                exit();
            }
        }
         $total = $this->calculateCartTotal();

         foreach ($cart as $productId => $quantity) {
             if (!$this->productModel->hasStock($productId, $quantity)) {
                 Session::flash('error', 'Some items are out of stock');
                 header('Location: /cart.php');
                 exit();
             }
         }

         $orderData = [
             'user_id' => Auth::id(),
             'total_amount' => $total,
             'status' => 'pending',
             'shipping_address' => $shippingAddress,
             'shipping_city' => $shippingCity,
             'shipping_postal_code' => $shippingPostalCode,
             'shipping_country' => $shippingCountry
         ];

         $orderId = $this->orderModel->create($orderData);

         if (!$orderId) {
             Session::flash('error', 'Order creation failed');
             header('Location: /checkout.php');
             exit();
         }

         $orderItems = [];
         foreach ($cart as $productId => $quantity) {
             $product = $this->productModel->findById($productId);
             $orderItems[] = [
                 'product_id' => $productId,
                 'quantity' => $quantity,
                 'price_at_purchase' => $product['price']
             ];

             $this->productModel->decreaseStock($productId, $quantity);
         }
         $this->orderModel->addItems($orderId, $orderItems);

         $user = Auth::user();
         Mailer::sendOrderConfirmation($user['email'], $orderId, $total);

         Session::remove('cart');

         Session::flash('success', 'Order placed successfully! Order ID: ' . $orderId);
         header('Location: /order-confirmation.php?id=' . $orderId);
         exit();
    }
}
