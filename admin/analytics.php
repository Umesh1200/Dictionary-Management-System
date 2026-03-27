<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Example query for analytics data
$totalWords = $pdo->query("SELECT COUNT(*) FROM words")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalTags = $pdo->query("SELECT COUNT(*) FROM tags")->fetchColumn();

include '../includes/header.php';
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f3f4f6;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-size: 28px;
        margin-bottom: 30px;
        color: #333;
    }

    .analytics-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .card {
        background: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
        text-align: center;
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h3 {
        margin-bottom: 10px;
        font-size: 20px;
        color: #555;
    }

    .card p {
        font-size: 26px;
        color: #1e90ff;
        font-weight: bold;
    }
</style>

<main class="container">
    <h2>📊 Analytics Overview</h2>
    
    <div class="analytics-stats">
        <div class="card">
            <h3>Total Words</h3>
            <p><?php echo $totalWords; ?></p>
        </div>
        <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>
        <div class="card">
            <h3>Total Categories</h3>
            <p><?php echo $totalCategories; ?></p>
        </div>
        <div class="card">
            <h3>Total Tags</h3>
            <p><?php echo $totalTags; ?></p>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
