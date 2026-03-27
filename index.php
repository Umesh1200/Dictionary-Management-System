
<?php
// index.php
require 'includes/config.php';
require 'includes/auth.php';
include 'includes/header.php';

// Fetch categories and tags
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags")->fetchAll();

// Fetch favorites if the user is logged in
$favWords = [];
if (!empty($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT w.id, w.word, w.definition FROM words w
                            JOIN favorites f ON w.id = f.word_id WHERE f.user_id = ?");
    $stmt->execute([$userId]);
    $favWords = $stmt->fetchAll();
}

// Handle favorite removal from index.php
if (isset($_POST['remove_favorite'])) {
    $wordIdToDelete = $_POST['word_id'];
    
    $deleteStmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND word_id = ?");
    if ($deleteStmt->execute([$userId, $wordIdToDelete])) {
        $msg = "Favorite removed successfully!";
    } else {
        $msg = "Error removing favorite.";
    }
}
?>

<style>
    /* Your styling here */
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f9fafb;
        margin: 0;
        padding: 0;
    }

    main .container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    h2 {
        color: #1e3a8a;
        text-align: center;
        margin-bottom: 10px;
    }

    p {
        text-align: center;
        color: #555;
        margin-bottom: 30px;
    }

    form {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 40px;
    }

    input[type="text"] {
        padding: 12px;
        font-size: 16px;
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    .btn {
        padding: 12px 20px;
        background-color: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background-color: #1e40af;
    }

    section {
        margin-bottom: 30px;
    }

    h3 {
        color: #1f2937;
        border-left: 5px solid #2563eb;
        padding-left: 10px;
        margin-bottom: 15px;
    }

    ul.categories-list,
    ul.tags-list {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;
        gap: 10px;
    }

    ul.categories-list li,
    ul.tags-list li {
        background-color: #f3f4f6;
        padding: 10px 15px;
        border-radius: 20px;
        transition: background 0.3s ease;
    }

    ul.categories-list li:hover,
    ul.tags-list li:hover {
        background-color: #dbeafe;
    }

    ul.categories-list li a,
    ul.tags-list li a {
        text-decoration: none;
        color: #2563eb;
        font-weight: 500;
    }

    .favorites-section {
        margin-top: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #2563eb;
        color: white;
    }

    .btn-small {
        padding: 6px 12px;
        font-size: 14px;
        background-color: #e3342f;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-small:hover {
        background-color: #cc1f1a;
    }

    @media (max-width: 600px) {
        form {
            flex-direction: column;
            align-items: center;
        }

        input[type="text"] {
            width: 100%;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<main>
    <div class="container">
        <h2>📘 Welcome to the Dictionary Site</h2>
        <p>Use the search bar to find word definitions quickly and easily.</p>
        
        <!-- Search Form -->
        <form action="search.php" method="get">
            <input type="text" name="query" placeholder="Enter a word..." required>
            <button type="submit" class="btn">Search</button>
        </form>

        <!-- Categories Section -->
        <section class="categories-section">
            <h3>📂 Browse by Categories</h3>
            <ul class="categories-list">
                <?php foreach ($categories as $category): ?>
                    <li><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <!-- Tags Section -->
        <section class="tags-section">
            <h3>🏷️ Browse by Tags</h3>
            <ul class="tags-list">
                <?php foreach ($tags as $tag): ?>
                    <li><a href="tag.php?id=<?php echo $tag['id']; ?>"><?php echo htmlspecialchars($tag['name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <!-- Favorites Section (Visible only if logged in) -->
        <?php if (!empty($_SESSION['user_id'])): ?>
            <section class="favorites-section">
                <h3>⭐ Your Favorite Words</h3>
                <?php if (isset($msg)): ?>
                    <p class="msg"><?php echo htmlspecialchars($msg); ?></p>
                <?php endif; ?>

                <?php if (empty($favWords)): ?>
                    <p>You have no favorite words yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Word</th>
                                <th>Definition</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($favWords as $fav): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fav['word']); ?></td>
                                    <td><?php echo htmlspecialchars($fav['definition']); ?></td>
                                    <td>
                                        <form action="index.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="word_id" value="<?php echo $fav['id']; ?>">
                                            <button type="submit" name="remove_favorite" class="btn-small">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
