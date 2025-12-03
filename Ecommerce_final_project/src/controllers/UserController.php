<?php
// User controller for handling user-related requests

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Mailer.php';

class UserController {

    private $userModel;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        $this->userModel = new User($dbConnection);
        Session::start();
    }

    /**
     * Show registration form
     */
    public function showRegisterForm() {
        Auth::requireGuest();

        require_once __DIR__ . '/../views/register.php';
    }

    /**
     * Handle user registration
     */
    public function register() {
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             http_response_code(405);
             die("Method not allowed");
         }

         $username = $_POST['username'] ?? '';
         $email = $_POST['email'] ?? '';
         $password = $_POST['password'] ?? '';
         $confirmPassword = $_POST['confirm_password'] ?? '';
         $firstName = $_POST['first_name'] ?? '';
         $lastName = $_POST['last_name'] ?? '';

         $validator = new Validator();
         $validator->required('username', $username)
                   ->min('username', $username, 3)
                   ->required('email', $email)
                   ->email('email', $email)
                   ->required('password', $password)
                   ->min('password', $password, 6)
                   ->matches('confirm_password', $confirmPassword, $password);

         if ($validator->fails()) {
             $errorMessages = implode(', ', $validator->getErrors());
             Session::flash('errors', $errorMessages);
             Session::flash('old_username', $username);
             Session::flash('old_email', $email);
             header('Location: /register.php');
             exit();
         }

         if ($this->userModel->emailExists($email)) {
             Session::flash('error', 'Email already registered');
             header('Location: /register.php');
             exit();
         }

         if ($this->userModel->usernameExists($username)) {
             Session::flash('error', 'Username already taken');
             header('Location: /register.php');
             exit();
         }

         $userData = [
             'username' => $username,
             'email' => $email,
             'password' => $password,
             'first_name' => $firstName,
             'last_name' => $lastName,
             'role' => 'customer'
         ];

         $userId = $this->userModel->create($userData);

         if ($userId) {
             Mailer::sendWelcomeEmail($email, $username);

             Session::flash('success', 'Registration successful! Please login.');

             header('Location: /login.php');
             exit();
         } else {
             Session::flash('error', 'Registration failed. Please try again.');
             header('Location: /register.php');
             exit();
         }
    }

    /**
     * Show login form
     */
    public function showLoginForm() {
        Auth::requireGuest();
        require_once __DIR__ . '/../views/login.php';
    }

    /**
     * Handle user login
     */
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $validator = new Validator();
        $validator->required('email', $email)
            ->email('email', $email)
            ->required('password', $password);

        if ($validator->fails()) {
            $errorMessages = implode(', ', $validator->getErrors());
            Session::flash('errors', $errorMessages);
            header('Location: /login.php');
            exit();
        }

        $user = $this->userModel->verifyCredentials($email, $password);

        if ($user) {
            Session::setUser($user['id'], [
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ]);

            Session::flash('success', 'Welcome back, ' . $user['username'] . '!');
            header('Location: /index.php');
            exit();
        } else {
            Session::flash('error', 'Invalid email or password');
            header('Location: /login.php');
            exit();
        }
    }

    /**
     * Handle user logout
     */
    public function logout() {
        Session::destroy();
        Session::start();
        Session::flash('info', 'You have been logged out');
        header('Location: /index.php');
        exit();
    }

    /**
     * Show user profile/account page
     */
    public function showProfile() {
         Auth::requireAuth();

         $userId = Auth::id();
         $user = $this->userModel->findById($userId);

         require_once __DIR__ . '/../models/Order.php';
         $orderModel = new Order($this->db);
         $orders = $orderModel->getByUserId($userId);

         require_once __DIR__ . '/../views/profile.php';
    }

    /**
     * Handle profile update
     */
    public function updateProfile() {
        Auth::requireAuth();

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            die("Method not allowed");
        }
        $userId = Auth::id();

        $username = $_POST['username'] ?? '';
        $email     = $_POST['email'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName  = $_POST['last_name'] ?? '';

        $validator = new Validator();
        $validator->required('username', $username)
            ->min('username', $username, 3)
            ->required('email', $email)
            ->email('email', $email);

        if($validator->fails()){
            $errorMessages = implode(', ', $validator->getErrors());
            Session::flash('errors', $errorMessages);
            header("Location: /profile.php");
            exit();
        }

        if($this->userModel->emailExists($email, $userId)){
            Session::flash('error', 'Email already in use');
            header('Location: /profile.php');
            exit();
        }

        if($this->userModel->usernameExists($username, $userId)){
            Session::flash('error', 'Username already in use');
            header('Location: /profile.php');
            exit();
        }

        $updateData = [
            'username' => $username,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
        $success = $this->userModel->update($userId, $updateData);

        if($success){
            Session::setUser($userId, [
                'username'=>$username,
                'email'=>$email,
            ]);

            Session::flash('success', 'Profile updated');
        }else{
            Session::flash('error', 'Update Failed');
        }
        header("Location: /profile.php");
        exit();
    }

    /**
     * Handle password change
     */
    public function changePassword() {
        Auth::requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die("Method not allowed");
        }

        $userId = Auth::id();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $validator = new Validator();
        $validator->required('current_password', $current)
            ->required('new_password', $new)
            ->min('new_password', $new, 6)
            ->matches('confirm_password', $confirm, $new);

        if ($validator->fails()) {
            $errorMessages = implode(', ', $validator->getErrors());
            Session::flash('errors', $errorMessages);
            header("Location: /profile.php");
            exit();
        }

        $user = $this->userModel->findById($userId);

        if (!password_verify($current, $user['password'])) {
            Session::flash('error', 'Current password incorrect');
            header("Location: /profile.php");
            exit();
        }

        $success = $this->userModel->updatePassword($userId, $new);

        if ($success) {
            Session::flash('success', 'Password updated successfully');
        } else {
            Session::flash('error', 'Password update failed');
        }

        header("Location: /profile.php");
        exit();
    }

    /**
     * Delete user account (optional feature)
     */
    public function deleteAccount() {
        Auth::requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die("Method not allowed");
        }

        $userId = Auth::id();

        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findById($userId);

        if (!password_verify($password, $user['password'])) {
            Session::flash('error', 'Password incorrect');
            header('Location: /profile.php');
            exit();
        }

        $success = $this->userModel->delete($userId);

        if ($success) {
            Session::destroy();
            Session::start();
            Session::flash('success', 'Account deleted');
            header('Location: /index.php');
            exit();
        }

        Session::flash('error', 'Failed to delete account');
        header('Location: /profile.php');
        exit();
    }
}