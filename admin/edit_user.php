<?php
// Includes
require '../includes/config.php';
require '../includes/auth.php';

// Redirect to login if the user is not an admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Check if an ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$userId = $_GET['id'];

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If no user found, show an error
if (!$user) {
    die("User not found.");
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Validate inputs
    if (empty($username) || empty($email) || !in_array($role, ['admin', 'user'])) {
        $message = "⚠️ Please fill in all fields correctly.";
    } else {
        // Update user data
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        if ($stmt->execute([$username, $email, $role, $userId])) {
            $message = "✅ User details updated successfully.";
        } else {
            $message = "❌ Error updating user details.";
        }
    }
}

include '../includes/header.php';
?>

<style>
/* Add styles for form */
.form-inline {
    display: inline-block;
    width: 100%;
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background: #f4f4f4;
    border-radius: 8px;
}

.form-inline input, .form-inline select {
    width: 100%;
    padding: 8px;
    margin: 10px 0;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.btn {
    background-color: #007bff;
    color: white;
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
}

.btn:hover {
    background-color: #0056b3;
}
</style>

<main class="container">
    <h2>Edit User</h2>

    <?php if (isset($message)): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <section class="form-section">
        <h3>Edit User Details</h3>
        <form method="POST" class="form-inline">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="role">Role</label>
            <select name="role" required>
                <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>

            <button type="submit" class="btn">Update User</button>
        </form>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
