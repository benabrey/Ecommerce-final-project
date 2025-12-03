<?php
// Product controller for handling product-related requests

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Validator.php';

class ProductController {

    private $productModel;

    public function __construct($dbConnection) {
        $this->productModel = new Product($dbConnection);
        Session::start();
    }

    /**
     * Show all products (product listing page)
     */
    public function index() {
        $page = $_GET['page'] ?? 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $category = $_GET['category'] ?? null;
        $search   = $_GET['search'] ?? null;

        if ($search) {
            $products = $this->productModel->search($search, $perPage, $offset);
        } elseif ($category) {
            $products = $this->productModel->getByCategory($category, $perPage, $offset);
        } else {
            $products = $this->productModel->getAll($perPage, $offset);
        }

        $categories = $this->productModel->getAllCategories();

        require_once __DIR__ . '/../views/products.php';
    }

    /**
     * Show single product details
     */
    public function show($id) {
        if (!$id || !is_numeric($id)) {
            Session::flash('error', 'Invalid product');
            header('Location: /products.php');
            exit();
        }

        $product = $this->productModel->findById($id);

        if (!$product) {
            Session::flash('error', 'Product not found');
            header('Location: /products.php');
            exit();
        }

        $relatedProducts = $this->productModel->getByCategory($product['category'], 4);

        require_once __DIR__ . '/../views/header.php';
        require_once __DIR__ . '/../views/navbar.php';
        require_once __DIR__ . '/../views/product_detail.php';
        require_once __DIR__ . '/../views/footer.php';
    }

    /**
     * Handle product creation (admin only)
     */
    public function create() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/products.php');
            exit();
        }

        $name          = Validator::sanitizeString($_POST['name'] ?? '');
        $description   = Validator::sanitizeString($_POST['description'] ?? '');
        $price         = Validator::sanitizeInt($_POST['price'] ?? 0);
        $stockQuantity = Validator::sanitizeInt($_POST['stock_quantity'] ?? 0);
        $category      = Validator::sanitizeString($_POST['category'] ?? '');
        $imageUrl      = Validator::sanitizeString($_POST['image_url'] ?? '');

        $validator = new Validator();
        $validator->required('name', $name)
            ->min('name', $name, 3)
            ->required('price', $price)
            ->numeric('price', $price)
            ->required('stock_quantity', $stockQuantity)
            ->numeric('stock_quantity', $stockQuantity);

        if ($validator->fails()) {
            $errorMessages = implode(', ', $validator->getErrors());
            Session::flash('error', $errorMessages);
            header('Location: /admin/products.php');
            exit();
        }

        $productData = [
            'name'           => $name,
            'description'    => $description,
            'price'          => $price,
            'stock_quantity' => $stockQuantity,
            'category'       => $category,
            'image_url'      => $imageUrl
        ];

        $productId = $this->productModel->create($productData);

        if ($productId) {
            Session::flash('success', 'Product created successfully.');
        } else {
            Session::flash('error', 'Failed to create product.');
        }

        header('Location: /admin/products.php');
        exit();
    }

    /**
     * Handle product update (admin only)
     */
    public function update($id) {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /admin/products.php");
            exit();
        }

        $name          = Validator::sanitizeString($_POST['name'] ?? '');
        $description   = Validator::sanitizeString($_POST['description'] ?? '');
        $price         = Validator::sanitizeInt($_POST['price'] ?? 0);
        $stockQuantity = Validator::sanitizeInt($_POST['stock_quantity'] ?? 0);
        $category      = Validator::sanitizeString($_POST['category'] ?? '');
        $imageUrl      = Validator::sanitizeString($_POST['image_url'] ?? '');

        $validator = new Validator();
        $validator->required('name', $name)
            ->min('name', $name, 3)
            ->required('price', $price)
            ->numeric('price', $price)
            ->required('stock_quantity', $stockQuantity)
            ->numeric('stock_quantity', $stockQuantity);

        if ($validator->fails()) {
            $errorMessages = implode(', ', $validator->getErrors());
            Session::flash('error', $errorMessages);
            header('Location: /admin/products.php');
            exit();
        }

        $updateData = [
            'name'           => $name,
            'description'    => $description,
            'price'          => $price,
            'stock_quantity' => $stockQuantity,
            'category'       => $category,
            'image_url'      => $imageUrl
        ];

        $success = $this->productModel->update($id, $updateData);

        if ($success) {
            Session::flash('success', 'Product updated successfully.');
        } else {
            Session::flash('error', 'Failed to update product.');
        }

        header('Location: /admin/products.php');
        exit();
    }

    /**
     * Handle product deletion (admin only)
     */
    public function delete($id) {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::flash('error', 'Invalid request method.');
            header('Location: /admin/products.php');
            exit();
        }

        $success = $this->productModel->delete($id);

        if ($success) {
            Session::flash('success', 'Product deleted successfully.');
        } else {
            Session::flash('error', 'Failed to delete product.');
        }

        header('Location: /admin/products.php');
        exit();
    }

    /**
     * Search products (AJAX endpoint)
     */
    public function search() {
        $searchTerm = $_GET['q'] ?? '';

        if (trim($searchTerm) === '') {
            echo json_encode([]);
            exit();
        }

        $products = $this->productModel->search($searchTerm, 10);

        header('Content-Type: application/json');
        echo json_encode($products);
        exit();
    }

    /**
     * Filter products by category
     */
    public function filterByCategory($category) {
        $page = $_GET['page'] ?? 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $products   = $this->productModel->getByCategory($category, $perPage, $offset);
        $categories = $this->productModel->getAllCategories();

        require_once __DIR__ . '/../views/products.php';
    }

    /**
     * Show admin product management dashboard
     */
    public function adminIndex() {
        Auth::requireAdmin();

        $products         = $this->productModel->getAll();
        $lowStockProducts = $this->productModel->getLowStock(10);
        $outOfStock       = $this->productModel->getOutOfStock();

        require_once __DIR__ . '/../views/admin/products.php';
    }

    /**
     * Update product stock (admin only)
     */
    public function updateStock() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']);
            exit();
        }

        $productId = $_POST['product_id'] ?? null;
        $newStock  = $_POST['stock_quantity'] ?? null;

        $validator = new Validator();
        $validator->required('product_id', $productId)
            ->numeric('product_id', $productId)
            ->required('stock_quantity', $newStock)
            ->numeric('stock_quantity', $newStock);

        if ($validator->fails()) {
            echo json_encode(['error' => $validator->getErrors()]);
            exit();
        }

        $success = $this->productModel->updateStock($productId, $newStock);

        echo json_encode(['success' => $success]);
        exit();
    }
}
