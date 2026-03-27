<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

include '../includes/header.php';

$totalWords = $pdo->query("SELECT COUNT(*) FROM words")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f7fa;
            color: #333;
        }

        main.container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .dashboard-stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            flex: 1 1 200px;
            background: #3498db;
            color: #fff;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        .card p {
            font-size: 2em;
            font-weight: bold;
        }

        .admin-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .btn {
            padding: 12px 20px;
            background: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #27ae60;
        }

        @media (max-width: 768px) {
            .dashboard-stats {
                flex-direction: column;
                align-items: center;
            }

            .admin-links {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<main class="container">
    <h2>📊 Admin Dashboard</h2>

    <div class="dashboard-stats">
        <div class="card">
            <h3>Total Words</h3>
            <p><?php echo $totalWords; ?></p>
        </div>
        <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="admin-links">
        <a href="manage_user.php" class="btn">👤 Manage Users</a>
        <a href="manage-words.php" class="btn">📝 Manage Words</a>
        <a href="manage-categories.php" class="btn">📂 Manage Categories</a>
        <a href="manage-tags.php" class="btn">🏷️ Manage Tags</a>
        <a href="import-export.php" class="btn">⬆⬇ Import / Export</a>
        <a href="analytics.php" class="btn">📈 Analytics</a>
        <a href="settings.php" class="btn">⚙️ Settings</a>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
