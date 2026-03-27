<?php
require 'includes/config.php';
require 'includes/auth.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}

include 'includes/header.php';
?>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f3f4f6;
        margin: 0;
        padding: 0;
    }

    .login-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        height: calc(100vh - 100px);
    }

    .login-box {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #1f2937;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    input[type="email"],
    input[type="password"] {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 8px;
        width: 100%;
    }

    button {
        padding: 12px;
        font-size: 16px;
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button:hover {
        background-color: #1e40af;
    }

    .error-message {
        color: #dc2626;
        background: #fee2e2;
        padding: 10px;
        border-radius: 6px;
        border-left: 5px solid #dc2626;
        font-size: 14px;
        margin-bottom: 20px;
    }

    @media (max-width: 600px) {
        .login-box {
            padding: 20px;
        }
    }
</style>

<main class="login-wrapper">
    <div class="login-box">
        <h2>🔐 Login</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Login</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
