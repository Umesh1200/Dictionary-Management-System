<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$message = "";

// Handle Add Word
if (isset($_POST['add_word'])) {
    $word = trim($_POST['word']);
    $definition = trim($_POST['definition']);
    $example = trim($_POST['example']);
    $categories = $_POST['categories'] ?? [];  // Categories selected
    $tags = $_POST['tags'] ?? [];  // Tags selected

    if ($word && $definition) {
        // Insert word into 'words' table
        $stmt = $pdo->prepare("INSERT INTO words (word, definition, example) VALUES (?, ?, ?)");
        $stmt->execute([$word, $definition, $example]);
        $wordId = $pdo->lastInsertId(); // Get the last inserted word ID

        // Insert categories into 'word_categories' table
        if (!empty($categories)) {
            foreach ($categories as $categoryId) {
                $stmt = $pdo->prepare("INSERT INTO word_categories (word_id, category_id) VALUES (?, ?)");
                $stmt->execute([$wordId, $categoryId]);
            }
        }

        // Insert tags into 'word_tags' table
        if (!empty($tags)) {
            foreach ($tags as $tagId) {
                $stmt = $pdo->prepare("INSERT INTO word_tags (word_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$wordId, $tagId]);
            }
        }

        $message = "✅ Word added successfully!";
    } else {
        $message = "⚠️ Please fill in all required fields.";
    }
}

// Handle Edit Word
if (isset($_POST['edit_word'])) {
    $id = $_POST['id'];
    $word = trim($_POST['word']);
    $definition = trim($_POST['definition']);
    $example = trim($_POST['example']);

    $stmt = $pdo->prepare("UPDATE words SET word = ?, definition = ?, example = ? WHERE id = ?");
    $stmt->execute([$word, $definition, $example, $id]);
    $message = "✏️ Word updated successfully!";
}

// Handle Delete Word
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM words WHERE id = ?")->execute([$id]);
    $message = "🗑️ Word deleted.";
}

// Fetch all words with their categories and tags
$words = $pdo->query("
    SELECT w.*, 
           GROUP_CONCAT(DISTINCT c.name) AS categories,
           GROUP_CONCAT(DISTINCT t.name) AS tags
    FROM words w
    LEFT JOIN word_categories wc ON w.id = wc.word_id
    LEFT JOIN categories c ON wc.category_id = c.id
    LEFT JOIN word_tags wt ON w.id = wt.word_id
    LEFT JOIN tags t ON wt.tag_id = t.id
    GROUP BY w.id
")->fetchAll();

// Fetch all categories and tags for selection in the form
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags")->fetchAll();

include '../includes/header.php';
?>
<?php
// ... [keep PHP code unchanged] ...
?>

<style>
/* Modern Color Scheme */
:root {
    --primary: #2563eb;
    --secondary: #4b5563;
    --success: #059669;
    --danger: #dc2626;
    --background: #f8fafc;
    --card-bg: #ffffff;
}

/* Base Styling */
body {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background-color: var(--background);
    line-height: 1.6;
    color: #1e293b;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

/* Card Layout */
.card {
    background: var(--card-bg);
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

/* Form Enhancements */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #334155;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 0.375rem;
    background-color: var(--card-bg);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

select.form-control {
    height: auto;
    min-height: 120px;
    padding: 0.5rem;
}

/* Tag Styling */
.tag-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.tag {
    background-color: #e2e8f0;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.875rem;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 0.375rem;
    font-weight: 500;
    transition: all 0.2s;
    cursor: pointer;
    border: 1px solid transparent;
}

.btn-primary {
    background-color: var(--primary);
    color: white;
}

.btn-primary:hover {
    background-color: #1d4ed8;
}

.btn-danger {
    background-color: var(--danger);
    color: white;
}

.btn-danger:hover {
    background-color: #b91c1c;
}

.btn-icon {
    padding: 0.5rem;
    border-radius: 0.25rem;
}

/* Table Enhancements */
.responsive-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.responsive-table th,
.responsive-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.responsive-table th {
    background-color: #f1f5f9;
    font-weight: 600;
    color: #334155;
}

.responsive-table tr:hover {
    background-color: #f8fafc;
}

.actions-cell {
    white-space: nowrap;
}

/* Message Styling */
.alert {
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #22c55e;
}

.alert-error {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .responsive-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="container">
    <h1 class="text-2xl font-bold mb-6">📚 Manage Dictionary</h1>
    
    <?php if ($message): ?>
        <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Add Word Form -->
    <div class="card">
        <h2 class="text-xl font-semibold mb-4">➕ Add New Word</h2>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Word</label>
                    <input type="text" name="word" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Definition</label>
                    <textarea name="definition" class="form-control" rows="2" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Example Usage</label>
                    <textarea name="example" class="form-control" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Categories (Ctrl+click to select multiple)</label>
                    <select name="categories[]" multiple class="form-control">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Tags (Ctrl+click to select multiple)</label>
                    <select name="tags[]" multiple class="form-control">
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="add_word" class="btn btn-primary">
                📥 Add Word
            </button>
        </form>
    </div>

    <!-- Word List -->
    <div class="card">
        <h2 class="text-xl font-semibold mb-4">📖 Existing Words</h2>
        <div class="table-container">
            <table class="responsive-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Word</th>
                        <th>Definition</th>
                        <th>Example</th>
                        <th>Categories</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($words as $word): ?>
                        <?php if (isset($_GET['edit']) && $_GET['edit'] == $word['id']): ?>
                            <!-- Edit Form -->
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $word['id'] ?>">
                                    <td><?= $word['id'] ?></td>
                                    <td>
                                        <input type="text" name="word" value="<?= htmlspecialchars($word['word']) ?>" 
                                               class="form-control" required>
                                    </td>
                                    <td>
                                        <textarea name="definition" class="form-control" required
                                        ><?= htmlspecialchars($word['definition']) ?></textarea>
                                    </td>
                                    <td>
                                        <textarea name="example" class="form-control"
                                        ><?= htmlspecialchars($word['example']) ?></textarea>
                                    </td>
                                    <td>
                                        <div class="tag-container">
                                            <?= implode('', array_map(fn($c) => "<span class='tag'>$c</span>", explode(',', $word['categories']))) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tag-container">
                                            <?= implode('', array_map(fn($t) => "<span class='tag'>$t</span>", explode(',', $word['tags']))) ?>
                                        </div>
                                    </td>
                                    <td class="actions-cell">
                                        <button type="submit" name="edit_word" class="btn btn-primary btn-icon">
                                            💾 Save
                                        </button>
                                        <a href="manage-words.php" class="btn btn-danger btn-icon">
                                            ✖ Cancel
                                        </a>
                                    </td>
                                </form>
                            </tr>
                        <?php else: ?>
                            <!-- Display Row -->
                            <tr>
                                <td><?= $word['id'] ?></td>
                                <td class="font-medium"><?= htmlspecialchars($word['word']) ?></td>
                                <td><?= nl2br(htmlspecialchars($word['definition'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($word['example'])) ?></td>
                                <td>
                                    <div class="tag-container">
                                        <?php foreach (explode(',', $word['categories']) as $cat): ?>
                                            <span class="tag"><?= $cat ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="tag-container">
                                        <?php foreach (explode(',', $word['tags']) as $tag): ?>
                                            <span class="tag"><?= $tag ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="actions-cell">
                                    <a href="manage-words.php?edit=<?= $word['id'] ?>" 
                                       class="btn btn-primary btn-icon">✏️ Edit</a>
                                    <a href="manage-words.php?delete=<?= $word['id'] ?>" 
                                       class="btn btn-danger btn-icon"
                                       onclick="return confirm('Are you sure you want to delete this word?')">🗑️ Delete</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>


