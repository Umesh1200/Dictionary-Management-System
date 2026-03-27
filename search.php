<?php
session_start(); // Start the session to access session variables
require 'includes/config.php';
require 'includes/header.php';

$results = [];
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

// If the search query exists
if ($query) {
    // Get the user ID from the session
    $userId = $_SESSION['user_id'];

    // Insert the search term into the search_history table
    $stmt = $pdo->prepare("INSERT INTO search_history (user_id, search_term, date_searched) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $query]);

    // Perform the search in the words table
    $stmt = $pdo->prepare("SELECT * FROM words WHERE word LIKE ? OR definition LIKE ? LIMIT 25");
    $stmt->execute(["%$query%", "%$query%"]);
    $results = $stmt->fetchAll();
}
?>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f9fafb;
    }

    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h2 {
        margin-bottom: 30px;
        font-size: 26px;
        color: #1f2937;
        text-align: center;
    }

    .search-results {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .search-results li {
        background: #fff;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 15px;
        border-radius: 8px;
        transition: box-shadow 0.3s ease;
    }

    .search-results li:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .search-results a {
        color: #0ea5e9;
        font-size: 18px;
        text-decoration: none;
    }

    .search-results a:hover {
        text-decoration: underline;
    }

    .definition-preview {
        font-size: 14px;
        color: #4b5563;
        margin-top: 6px;
    }

    .no-results {
        text-align: center;
        font-size: 16px;
        color: #9ca3af;
        margin-top: 40px;
    }

    @media (max-width: 600px) {
        .container {
            margin-top: 20px;
        }

        h2 {
            font-size: 22px;
        }

        .search-results li {
            padding: 12px;
        }

        .search-results a {
            font-size: 16px;
        }
    }
</style>

<main class="container">
    <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>

    <?php if (count($results) > 0): ?>
        <ul class="search-results">
            <?php foreach ($results as $word): ?>
                <li>
                    <a href="word-detail.php?id=<?php echo $word['id']; ?>">
                        <?php echo htmlspecialchars($word['word']); ?>
                    </a>
                    <p class="definition-preview">
                        <?php echo nl2br(htmlspecialchars(substr($word['definition'], 0, 100))); ?>...
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="no-results">No results found for "<?php echo htmlspecialchars($query); ?>".</p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
