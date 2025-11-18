<?php
// Session management helper class

class Session {

    /**
     * Start the session if not already started
     */
    public static function start() {
        if(session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');

            session_start();
        }
    }

    /**
     * Set a session variable
     * @param string $key The session key
     * @param mixed $value The value to store
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;

    }

    /**
     * Get a session variable
     * @param string $key The session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The session value or default
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;

    }

    /**
     * Check if a session variable exists
     * @param string $key The session key
     * @return bool True if exists, false otherwise
     */
    public static function has($key) {
        self::start();
            return isset($_SESSION[$key]);

    }

    /**
     * Remove a session variable
     * @param string $key The session key to remove
     */
    public static function remove($key) {
        self::start();
            unset($_SESSION[$key]);

    }

    /**
     * Destroy the entire session
     */
    public static function destroy() {
        self::start();
        $_SESSION = [];
        if(isset($_COOKIE[session_name()])){
            setcookie(
                session_name(),
                '',
                time() - 3600,
                '/',
                '',
                false,
                true
            );
        }
        session_destroy();
    }

    /**
     * Regenerate session ID (security measure)
     */
    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Set a flash message (message that persists for one request)
     * @param string $key The flash message key
     * @param string $message The message to flash
     */
    public static function flash($key, $message) {
        self::start();
        if(!isset($_SESSION['flash'])){
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get and remove a flash message
     * @param string $key The flash message key
     * @return string|null The flash message or null
     */
    public static function getFlash($key) {
        self::start();
        if(isset($_SESSION['flash'][$key])){
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Check if user is logged in
     * @return bool True if logged in, false otherwise
     */
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get the logged-in user's ID
     * @return int|null User ID or null if not logged in
     */
    public static function getUserId() {
        self::start();
        if(self::isLoggedIn()){
            return $_SESSION['user_id'];
        }
        return null;
    }

    /**
     * Set the logged-in user
     * @param int $userId The user's ID
     * @param array $userData Additional user data to store
     */
    public static function setUser($userId, $userData = []) {
        self::start();
        $_SESSION['user_id'] = $userId;
        foreach($userData as $key => $value){
            $_SESSION['user_' . $key] = $value;
        }
        self::regenerate();
    }

    /**
     * Log out the current user
     */
    public static function logout() {
        self::start();
        $keysToRemove = [];
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'user') === 0) {
                $keysToRemove[] = $key;
            }
        }
        foreach ($keysToRemove as $key) {
            unset($_SESSION[$key]);
        }
    }
}