<?php
require 'includes/config.php';
require 'includes/auth.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $message = "Email is already taken. Please use a different one.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$username, $email, $passwordHash]);
                $message = "✅ Registration successful. You can now log in.";
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $message = "❌ There was an issue with your registration. Please try again.";
            }
        }
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

    .register-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 50px 20px;
        min-height: calc(100vh - 100px);
    }

    .register-box {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 450px;
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

    input {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 8px;
        width: 100%;
    }

    button {
        padding: 12px;
        font-size: 16px;
        background-color: #10b981;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button:hover {
        background-color: #059669;
    }

    .message {
        padding: 12px;
        border-radius: 6px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .message-success {
        background-color: #e0f9e7;
        color: #1b7f47;
        border-left: 5px solid #10b981;
    }

    .message-error {
        background-color: #fde8e8;
        color: #b91c1c;
        border-left: 5px solid #dc2626;
    }

    @media (max-width: 600px) {
        .register-box {
            padding: 25px;
        }
    }
</style>

<main class="register-wrapper">
    <div class="register-box">
        <h2>📝 Create an Account</h2>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'message-success' : 'message-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit">Register</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
