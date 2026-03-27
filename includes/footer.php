<?php
// Includes
require 'config.php';  // Make sure the PDO connection is included

// Fetch site name and footer text from settings table
$stmt = $pdo->prepare("SELECT site_name, footer_text FROM settings LIMIT 1");
$stmt->execute();
$currentSettings = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch both site name and footer text

// If no settings found, use default values
if (!$currentSettings) {
    $siteName = "My Dictionary";  // Default site name
    $footerText = "Powered by My Dictionary"; // Default footer text
} else {
    $siteName = $currentSettings['site_name'];
    $footerText = $currentSettings['footer_text'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?></title>
    <!-- Add other necessary meta tags and links here -->
</head>
<body>
    <!-- Your main content goes here -->

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. <?php echo htmlspecialchars($footerText); ?> All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
