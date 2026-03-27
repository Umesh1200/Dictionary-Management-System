<?php
require 'includes/config.php';
require 'includes/auth.php';
include 'includes/header.php';

$tagId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$tag = $pdo->prepare("SELECT * FROM tags WHERE id = ?");
$tag->execute([$tagId]);
$tag = $tag->fetch();

if (!$tag) {
    echo "<div class='container'><p class='error-msg'>Tag not found.</p></div>";
    include 'includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("
    SELECT w.* 
    FROM words w 
    JOIN word_tags wt ON w.id = wt.word_id 
    WHERE wt.tag_id = ?");
$stmt->execute([$tagId]);
$words = $stmt->fetchAll();
?>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f3f4f6;
    }

    .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h2 {
        font-size: 28px;
        color: #1f2937;
        margin-bottom: 25px;
        text-align: center;
    }

    .words-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .words-list li {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease;
    }

    .words-list li:hover {
        transform: translateY(-2px);
    }

    .words-list strong {
        font-size: 18px;
        color: #0ea5e9;
    }

    .words-list p {
        margin-top: 8px;
        font-size: 15px;
        color: #374151;
        white-space: pre-line;
    }

    .error-msg {
        text-align: center;
        color: #dc2626;
        font-size: 18px;
        margin-top: 40px;
    }

    @media (max-width: 600px) {
        .container {
            margin-top: 20px;
        }

        h2 {
            font-size: 24px;
        }

        .words-list li {
            padding: 14px;
        }

        .words-list strong {
            font-size: 16px;
        }

        .words-list p {
            font-size: 14px;
        }
    }
</style>

<main>
    <div class="container">
        <h2>Words with Tag: <?php echo htmlspecialchars($tag['name']); ?></h2>
        
        <?php if (count($words) > 0): ?>
            <ul class="words-list">
                <?php foreach ($words as $word): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($word['word']); ?></strong>
                        <p><?php echo nl2br(htmlspecialchars($word['definition'])); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="error-msg">No words found with this tag.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
