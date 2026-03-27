<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = "";

// Fetch user data
$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$userId]);
$userData = $user->fetch();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Update user information in the database
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$username, $email, $userId]);

    // Update the session variable to reflect the new username
    $_SESSION['username'] = $username;  // This will update the session with the new username
    
    $message = "✅ Profile updated successfully!";
}

include '../includes/header.php';
?>

<style>
    /* Profile Page Styling */
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
</style>

<main class="container">
    <h2>User Profile.</h2> <!-- Added period after "User Profile" -->
    
    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <section class="form-section">
        <h3>Edit Profile</h3>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required><br><br>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required><br><br>
            
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
