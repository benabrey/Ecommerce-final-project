<?php
// Authentication and authorization helper class

require_once __DIR__ . '/Session.php';

class Auth {

    /**
     * Check if user is authenticated
     * @return bool True if authenticated, false otherwise
     */
    public static function check() {
        return Session::isLoggedIn();
    }

    /**
     * Check if user is a guest (not logged in)
     * @return bool True if guest, false otherwise
     */
    public static function guest() {
        return !Auth::check();
    }

    /**
     * Get the authenticated user's ID
     * @return int|null User ID or null
     */
    public static function id() {
        $id = Session::getUserId();
        return $id ?: null;
    }

    /**
     * Get the authenticated user's data
     * @return array|null User data or null
     */
    public static function user() {
        if(!self::check()){
            return null;
        }
        $userData = [
            'id' => Session::get('user_id')
        ];
        foreach($_SESSION as $key => $value){
            if(strpos($key, 'user_')===0){
                $cleanKey = substr($key, 5);
                $userData[$cleanKey] = $value;
            }
        }
        return $userData;
    }

    /**
     * Check if user has a specific role
     * @param string $role Role to check (e.g., 'admin', 'customer')
     * @return bool True if user has role, false otherwise
     */
    public static function hasRole($role) {
        $user = self::user();
        if(!$user){
            return false;
        }
        return isset($user['role']) && strtolower($user['role']) === strtolower($role);
    }

    /**
     * Check if user is an admin
     * @return bool True if admin, false otherwise
     */
    public static function isAdmin() {
        return self::hasRole('admin');
    }

    /**
     * Check if user is a customer
     * @return bool True if customer, false otherwise
     */
    public static function isCustomer() {
        return self::hasRole('customer');
    }

    /**
     * Require authentication (redirect if not logged in)
     * @param string $redirectTo URL to redirect to if not authenticated
     */
    public static function requireAuth($redirectTo = '/index.php') {
        if(!self::check()){
            Session::flash('error', 'Please login to continue');
            header('Location: ' . $redirectTo);
            exit();
        }
    }

    /**
     * Require guest (redirect if logged in)
     * @param string $redirectTo URL to redirect to if authenticated
     */
    public static function requireGuest($redirectTo = '/index.php') {
        if(self::check()){
            header('Location: ' . $redirectTo);
            exit();
        }

    }

    /**
     * Require admin role (redirect if not admin)
     * @param string $redirectTo URL to redirect to if not admin
     */
    public static function requireAdmin($redirectTo = '/index.php') {
        if(!self::check()){
            Session::flash('error', 'Please login to continue');
            header('Location: ' . '/login.php');
            exit();
        }
        if(!self::isAdmin()){
            Session::flash('error', 'Access denied. Admin privileges required');
            header('Location: ' . $redirectTo);
            exit();
        }
    }

    /**
     * Log out the current user
     */
    public static function logout() {
        Session::logout();
        header('Location: ' . '/index.php');
        exit();

    }

    /**
     * Hash a password
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public static function hashPassword($password) {
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        return $hashedPass;
    }

    /**
     * Verify a password against a hash
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool True if password matches, false otherwise
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);

    }
}