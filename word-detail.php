<?php
require 'includes/config.php';
require 'includes/auth.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM words WHERE id = ?");
$stmt->execute([$id]);
$word = $stmt->fetch();

if (!$word) {
    echo "<main class='container'><h2 class='not-found'>Word not found.</h2></main>";
    include 'includes/footer.php';
    exit;
}

// Fetch categories
$catStmt = $pdo->prepare("SELECT c.name FROM categories c 
                          JOIN word_categories wc ON c.id = wc.category_id 
                          WHERE wc.word_id = ?");
$catStmt->execute([$id]);
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch tags
$tagStmt = $pdo->prepare("SELECT t.name FROM tags t 
                          JOIN word_tags wt ON t.id = wt.tag_id 
                          WHERE wt.word_id = ?");
$tagStmt->execute([$id]);
$tags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
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

    p {
        font-size: 16px;
        color: #374151;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .label {
        font-weight: bold;
        color: #2563eb;
    }

    .badge {
        display: inline-block;
        background-color: #e0f2fe;
        color: #0369a1;
        font-size: 14px;
        padding: 4px 10px;
        border-radius: 20px;
        margin: 3px 6px 3px 0;
    }

    .btn-favorite {
        background-color: #ef4444;
        color: white;
        border: none;
        padding: 10px 15px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s;
        display: block;
        margin-top: 20px;
    }

    .btn-favorite:hover {
        background-color: #dc2626;
    }

    .success-msg, .info-msg {
        padding: 10px;
        border-radius: 6px;
        text-align: center;
        margin-bottom: 10px;
    }

    .success-msg {
        color: #10b981;
        background: #ecfdf5;
    }

    .info-msg {
        color: #f59e0b;
        background: #fff7ed;
    }
</style>

<main class="container">
    <h2><?php echo htmlspecialchars($word['word']); ?></h2>

    <p><span class="label">Definition:</span> <?php echo nl2br(htmlspecialchars($word['definition'])); ?></p>

    <?php if (!empty($word['example'])): ?>
        <p><span class="label">Example:</span> <em><?php echo htmlspecialchars($word['example']); ?></em></p>
    <?php endif; ?>

    <?php if ($categories): ?>
        <p><span class="label">Categories:</span>
            <?php foreach ($categories as $cat): ?>
                <span class="badge"><?php echo htmlspecialchars($cat); ?></span>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>

    <?php if ($tags): ?>
        <p><span class="label">Tags:</span>
            <?php foreach ($tags as $tag): ?>
                <span class="badge"><?php echo htmlspecialchars($tag); ?></span>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>

    <!-- Show Success Message -->
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'favorited'): ?>
            <p class="success-msg">✅ Word added to favorites!</p>
        <?php elseif ($_GET['msg'] === 'already_favorited'): ?>
            <p class="info-msg">⚠️ This word is already in your favorites.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Favorite Button -->
    <?php if (!empty($_SESSION['user_id'])): ?>
        <form action="add_favorite.php" method="post">
            <input type="hidden" name="word_id" value="<?php echo $id; ?>">
            <button type="submit" class="btn-favorite">❤️ Add to Favorites</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to add this word to favorites.</p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
