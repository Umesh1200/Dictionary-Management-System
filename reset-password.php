<?php
require 'includes/config.php';
require 'includes/auth.php';

$message = "";

// Handle the password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];

    // Check if the token exists and is valid
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expire_at > NOW()");
    $stmt->execute([$token]);
    $resetRequest = $stmt->fetch();

    if ($resetRequest) {
        // Update the user's password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $resetRequest['email']]);

        // Delete the reset token as it's no longer needed
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        $message = "Your password has been successfully reset. You can now log in with your new password.";
    } else {
        $message = "Invalid or expired token.";
    }
}

$token = $_GET['token'] ?? null;
include 'includes/header.php';
?>

<main class="container">
    <h2>Reset Password</h2>

    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <?php if ($token): ?>
        <section class="form-section">
            <h3>Enter New Password</h3>
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required><br><br>
                
                <button type="submit" class="btn">Reset Password</button>
            </form>
        </section>
    <?php else: ?>
        <p class="error">Invalid request.</p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
