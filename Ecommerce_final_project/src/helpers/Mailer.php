<?php
// Email sending helper class

class Mailer {

    private $to;
    private $from;
    private $subject;
    private $body;
    private $headers = [];

    /**
     * Set the recipient email address
     * @param string $email Recipient email
     * @return $this For method chaining
     */
    public function to($email) {
        $this->to = $email;
        return $this;
    }

    /**
     * Set the sender email address
     * @param string $email Sender email
     * @param string $name Sender name (optional)
     * @return $this For method chaining
     */
    public function from($email, $name = null) {
        if($name){
            $this-> from = "$name <$email>";
        }else{
            $this->from = $email;
        }
        return $this;
    }

    /**
     * Set the email subject
     * @param string $subject Email subject
     * @return $this For method chaining
     */
    public function subject($subject) {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set the email body
     * @param string $body Email body content
     * @param bool $isHtml Whether body is HTML (default: false)
     * @return $this For method chaining
     */
    public function body($body, $isHtml = false) {
        $this->body = $body;
        if($isHtml){
            $this->headers[] = "MIME-Version 1.0";
            $this->headers[] = "Content-type: text/html; charset=UTF-8";
        }
        return $this;
    }

    /**
     * Add a custom header
     * @param string $header Header name
     * @param string $value Header value
     * @return $this For method chaining
     */
    public function addHeader($header, $value) {
        $this->headers[] = "$header: $value";
        return $this;
    }

    /**
     * Send the email
     * @return bool True if sent successfully, false otherwise
     */
    public function send() {
        if (empty($this->to) || empty($this->from) || empty($this->subject) || empty($this->body)) {
            return false;
        }

        $this->headers = $this->headers ?? [];

        if (!array_filter($this->headers, fn($h) => str_starts_with(strtolower($h), 'from:'))) {
            $this->headers[] = "From: {$this->from}";
        }

        $headersString = implode("\r\n", $this->headers);

        return mail($this->to, $this->subject, $this->body, $headersString);
    }

    /**
     * Send a welcome email to new users
     * @param string $email User's email
     * @param string $username User's username
     * @return bool True if sent, false otherwise
     */
    public static function sendWelcomeEmail($email, $username) {
        $mailer = new self();
        $subject = "Welcome to our store!";
        $body = "<h1>Welcome, " . htmlspecialchars($username) . "!</h1>
             <p>Thank you for signing up. We're excited to have you on board.</p>";

        return $mailer->to($email)
                        ->from("no-reply@yourstore.com", "Store")
                        -> subject($subject)
                        ->body($body, true)
                        ->send();
    }

    /**
     * Send order confirmation email
     * @param string $email Customer's email
     * @param int $orderId Order ID
     * @param float $total Order total
     * @return bool True if sent, false otherwise
     */
    public static function sendOrderConfirmation($email, $orderId, $total) {
        $mailer = new self();
        $subject = "Order Confirmation - Order #$orderId";
        $body = "<h1>Thank you for your order!</h1>
             <p>Your order <strong>#$orderId</strong> has been successfully placed.</p>
             <p>Total Amount: <strong>$" . number_format($total, 2) . "</strong></p>
             <p>We will notify you when your order ships.</p>";

        return $mailer->to($email)
            ->from("no-reply@yourstore.com", "Your Store")
            ->subject($subject)
            ->body($body, true)
            ->send();
    }

    /**
     * Send password reset email
     * @param string $email User's email
     * @param string $resetToken Password reset token
     * @return bool True if sent, false otherwise
     */
    public static function sendPasswordReset($email, $resetToken) {
        $mailer = new self();
        $subject = "Password Reset Request";
        $resetLink = "https://yourstore.com/reset-password.php?token=" . urlencode($resetToken);
        $body = "<h1>Password Reset Request</h1>
             <p>Click the link below to reset your password:</p>
             <p><a href='$resetLink'>$resetLink</a></p>
             <p>If you didn't request a password reset, you can ignore this email.</p>";

        return $mailer->to($email)
            ->from("no-reply@yourstore.com", "Your Store")
            ->subject($subject)
            ->body($body, true)
            ->send();
    }

    /**
     * Send shipping confirmation email
     * @param string $email Customer's email
     * @param int $orderId Order ID
     * @param string $trackingNumber Tracking number
     * @return bool True if sent, false otherwise
     */
    public static function sendShippingConfirmation($email, $orderId, $trackingNumber) {
        $mailer = new self();
        $subject = "Your Order #$orderId has Shipped!";
        $body = "<h1>Good news!</h1>
             <p>Your order <strong>#$orderId</strong> has been shipped.</p>
             <p>Tracking Number: <strong>$trackingNumber</strong></p>
             <p>You can track your shipment <a href='https://www.canadapost.ca/track/?trackingNumber=$trackingNumber'>here</a>.</p>";

        return $mailer->to($email)
            ->from("no-reply@yourstore.com", "Your Store")
            ->subject($subject)
            ->body($body, true)
            ->send();
    }
}