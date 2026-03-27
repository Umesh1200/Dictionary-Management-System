<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$message = "";

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Delete User via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }

    $id = $_POST['delete'];
    
    if ($id == $_SESSION['user_id']) {
        $message = "⚠️ You cannot delete your own account.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "🗑️ User deleted successfully.";
        } else {
            $message = "❌ Error deleting user.";
        }
    }
}

// Fetch all users
$users = $pdo->query("SELECT * FROM users")->fetchAll();

include '../includes/header.php';
?>

<style>
.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table th, .table td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

.table th {
    background: #007bff;
    color: white;
    font-weight: bold;
}

.table td {
    background: #f9f9f9;
}

.table tr:hover {
    background: #f1f1f1;
}

.btn-small {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    font-size: 14px;
    transition: 0.3s;
}

.btn-small:hover {
    opacity: 0.8;
}

.btn-edit {
    background: #28a745;
}

.btn-delete {
    background: #dc3545;
    border: none;
    cursor: pointer;
}

.success {
    color: #28a745;
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}

.form-inline {
    display: inline;
}


</style>

<main class="container">
    <h2>Manage Users</h2>
    
    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <section class="table-section">
        <h3>Existing Users</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-small btn-edit">✏️ Edit</a>
                            <form method="post" action="manage_user.php" class="form-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="delete" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit" class="btn-small btn-delete">🗑 Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    
</main>
