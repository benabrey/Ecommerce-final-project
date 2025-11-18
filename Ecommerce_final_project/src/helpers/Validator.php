<?php
// Input validation and sanitization helper class

class Validator {

    private $errors = [];

    /**
     * Validate required field
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $message Custom error message
     * @return $this For method chaining
     */
    public function required($field, $value, $message = null) {
        if(empty(trim($value))){
            $this->errors[$field] = $message ?? "The $field field is required!";
        }
        return $this;
    }

    /**
     * Validate email format
     * @param string $field Field name
     * @param string $value Email value
     * @param string $message Custom error message
     * @return $this For method chaining
     */
    public function email($field, $value, $message = null) {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
            $this->errors[$field] = $message ?? "The $field must be a valid email address";
        }
        return $this;
    }

    /**
     * Validate minimum length
     * @param string $field Field name
     * @param string $value Field value
     * @param int $min Minimum length
     * @param string $message Custom error message
     * @return $this For method chaining
     */
    public function min($field, $value, $min, $message = null) {
        if(strlen($value) < $min){
            $this->errors[$field] = $message ?? "The $field must be at least $min long";
        }
        return $this;
    }

    /**
     * Validate maximum length
     * @param string $field Field name
     * @param string $value Field value
     * @param int $max Maximum length
     * @param string $message Custom error message
     * @return $this For method chaining
     */
    public function max($field, $value, $max, $message = null) {
        if(strlen($value) > $max){
            $this->errors[$field] = $message ?? "The $field must not be longer than $max";
        }
        return $this;
    }

    /**
     * Validate that value matches another field (for password confirmation)
     * @param string $field Field name
     * @param mixed $value Field value
     * @param mixed $match Value to match against
     * @param string $message Custom error message
     * @return $this For method chaining
     */
    public function matches($field, $value, $match, $message = null) {
        if($value !== $match){
            $this->errors[$field] = $message ?? "The $field confirmation does not match";
        }
        return $this;
    }

    /**
     * Validate numeric value
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $message Custom error message
     * @return $this For method chaining
     */
    public function numeric($field, $value, $message = null) {
        if(!is_numeric($value)){
            $this->errors[$field] = $message ?? "The $field needs to be numeric";
        }
        return $this;
    }

    /**
     * Validate value is within a range
     * @param string $field Field name
     * @param numeric $value Field value
     * @param numeric $min Minimum value
     * @param numeric $max Maximum value
     * @param string $message Custom error message
     * @return $this For method chaining
     */
    public function between($field, $value, $min, $max, $message = null) {
        if($value < $min || $value > $max){
            $this->errors[$field]=$message ?? "The $field must be between $min and $max";
        }
        return $this;
    }

    /**
     * Check if validation passed
     * @return bool True if no errors, false otherwise
     */
    public function passes() {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     * @return bool True if has errors, false otherwise
     */
    public function fails() {
        return !empty($this->errors);
    }

    /**
     * Get all validation errors
     * @return array Array of errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get first error for a field
     * @param string $field Field name
     * @return string|null First error or null
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }

    /**
     * Sanitize string input
     * @param string $input Input to sanitize
     * @return string Sanitized input
     */
    public static function sanitizeString($input) {
        $input = trim($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }

    /**
     * Sanitize email input
     * @param string $email Email to sanitize
     * @return string Sanitized email
     */
    public static function sanitizeEmail($email) {
        $email = filter_var($email,FILTER_SANITIZE_EMAIL);
        $email = trim($email);
        $email = strtolower($email);
        return $email;
    }

    /**
     * Sanitize integer input
     * @param mixed $input Input to sanitize
     * @return int Sanitized integer
     */
    public static function sanitizeInt($input) {
        $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        $input = (int)$input;
        return $input;
    }
}