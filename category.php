<?php
// category.php
require 'includes/config.php';
require 'includes/auth.php';
include 'includes/header.php';

// Get category ID from URL
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch category name
$category = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$category->execute([$categoryId]);
$category = $category->fetch();

if (!$category) {
    echo "Category not found.";
    exit;
}

// Fetch words associated with the category
$stmt = $pdo->prepare("
    SELECT w.* 
    FROM words w 
    JOIN word_categories wc ON w.id = wc.word_id 
    WHERE wc.category_id = ?");
$stmt->execute([$categoryId]);
$words = $stmt->fetchAll();
?>

<main>
    <div class="container">
        <h2>Words in Category: <?php echo htmlspecialchars($category['name']); ?></h2>
        
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
            <p>No words found in this category.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
