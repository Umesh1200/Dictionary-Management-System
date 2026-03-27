<?php
require '../includes/config.php';
require '../includes/auth.php';
include '../includes/header.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = "";

// Check for success message in URL
if (isset($_GET['msg']) && $_GET['msg'] === 'removed') {
    $message = "✅ Word removed from favorites.";
}

// Handle the delete request if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    
    // Delete the favorite word
    $deleteStmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND word_id = ?");
    if ($deleteStmt->execute([$userId, $deleteId])) {
        // header("Location: favorites.php?msg=removed");
        header("Location: /dictionary-site/user/favorites.php?msg=removed");
        exit;
    } else {
        $message = "❌ Failed to remove the word.";
    }
}

// Fetch favorite words for the logged-in user
$stmt = $pdo->prepare("SELECT w.id, w.word, w.definition FROM favorites f 
                       JOIN words w ON f.word_id = w.id 
                       WHERE f.user_id = ?");
$stmt->execute([$userId]);
$favorites = $stmt->fetchAll();
?>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f9fafb;
    }

    .container {
        max-width: 800px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }

    h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #1e3a8a;
    }

    .favorite-item {
        background: #f3f4f6;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .remove-btn {
        background-color: #ef4444;
        color: white;
        border: none;
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .remove-btn:hover {
        background-color: #dc2626;
    }

    .success-msg {
        color: #10b981;
        background: #ecfdf5;
        padding: 10px;
        border-radius: 6px;
        text-align: center;
        margin-bottom: 10px;
    }
</style>

<main class="container">
    <h2>⭐ My Favorite Words</h2>

    <!-- Success Message -->
    <?php if (!empty($message)): ?>
        <p class="success-msg"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if (empty($favorites)): ?>
        <p>No favorite words added yet.</p>
    <?php else: ?>
        <?php foreach ($favorites as $word): ?>
            <div class="favorite-item">
                <div>
                    <strong><?php echo htmlspecialchars($word['word']); ?></strong><br>
                    <small><?php echo htmlspecialchars($word['definition']); ?></small>
                </div>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?php echo $word['id']; ?>">
                    <button type="submit" class="remove-btn">❌ Remove</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
