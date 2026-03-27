<?php
// Includes
require 'config.php';  // Ensure PDO connection is included

// Fetch site name from the settings table
$stmt = $pdo->prepare("SELECT site_name FROM settings LIMIT 1");
$stmt->execute();
$siteName = $stmt->fetchColumn();  // Get the site name from the settings table

// If no site name found, use default value
if (!$siteName) {
    $siteName = "My Dictionary"; // Default site name
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?></title> <!-- Use dynamic site name in title -->
    <link rel="stylesheet" href="/dictionary-site/assets/css/main.css">
    <style>
        /* Quick styling in case external CSS is minimal */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }

        header {
            background-color: #343a40;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1rem;
        }

        nav ul li a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.2s ease;
        }

        nav ul li a:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>
<header>
    <h1>📘 <?php echo htmlspecialchars($siteName); ?></h1> <!-- Display dynamic site name in header -->
    <nav>
        <ul>
            <li><a href="/dictionary-site/index.php">Home</a></li>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <li><a href="/dictionary-site/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <li><a href="/dictionary-site/user/profile.php">Profile</a></li>
                <li><a href="/dictionary-site/user/settings.php">Settings</a></li> <!-- Link to Settings -->
                <li><a href="/dictionary-site/user/history.php">Search History</a></li> <!-- Link to History -->
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="/dictionary-site/admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="/dictionary-site/login.php">Login</a></li>
                <li><a href="/dictionary-site/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
