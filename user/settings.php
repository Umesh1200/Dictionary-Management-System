<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit;
}

$message = "";
$userId = $_SESSION['user_id'];

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $newPassword = $_POST['new_password'];

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        // Change the column name to 'password_hash' or whatever the correct name is
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
        $message = "✅ Password changed successfully!";
    } else {
        $message = "⚠️ Please enter a valid password.";
    }
}


include '../includes/header.php';
?>

<style>
    /* Settings Page Styling */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        background-color: #f8f9fa;
        padding: 20px;
    }

    .container {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #343a40;
    }

    .form-section {
        margin-top: 20px;
    }

    .form-section h3 {
        color: #495057;
        font-size: 1.4rem;
        margin-bottom: 20px;
    }

    .form-section form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-section label {
        font-weight: bold;
        color: #343a40;
    }

    .form-section input {
        padding: 10px;
        font-size: 1rem;
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    .form-section button {
        padding: 10px 20px;
        font-size: 1rem;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .form-section button:hover {
        background-color: #0056b3;
    }

    .success {
        color: #28a745;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    .warning {
        color: #e3342f;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }
</style>

<main class="container">
    <h2>User Settings</h2>

    <?php if ($message): ?>
        <p class="<?php echo strpos($message, '⚠️') !== false ? 'warning' : 'success'; ?>"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Change Password Form -->
    <section class="form-section">
        <h3>Change Password</h3>
        <form method="POST">
            <input type="password" name="new_password" placeholder="New Password" required><br><br>
            <button type="submit" class="btn">Save New Password</button>
        </form>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
