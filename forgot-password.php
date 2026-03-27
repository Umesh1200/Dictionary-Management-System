<!-- forgot-password.php content placeholder -->
<?php
require 'includes/config.php';
require 'includes/auth.php';

$message = "";

// Handle the password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate a password reset token
        $token = bin2hex(random_bytes(50));
        
        // Store the token in the database with an expiration time
        $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expire_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expire]);
        
        // Send the reset link to the user's email
        $resetLink = "http://localhost/dictionary-site/reset-password.php?token=$token";
        $subject = "Password Reset Request";
        $body = "To reset your password, click the link below:\n\n$resetLink";
        $headers = "From: no-reply@yourdomain.com";
        
        if (mail($email, $subject, $body, $headers)) {
            $message = "A password reset link has been sent to your email.";
        } else {
            $message = "There was an issue sending the email. Please try again.";
        }
    } else {
        $message = "No account found with that email address.";
    }
}

include 'includes/header.php';
?>

<main class="container">
    <h2>Forgot Password</h2>
    
    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <section class="form-section">
        <h3>Enter Your Email Address</h3>
        <form method="POST">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required><br><br>
            
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
