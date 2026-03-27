<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$message = "";

// Fetch current settings from the database
$stmt = $pdo->prepare("SELECT * FROM settings LIMIT 1");
$stmt->execute();
$currentSettings = $stmt->fetch();

if (!$currentSettings) {
    die("Settings not found in the database.");
}

// Default values fetched from the database
$defaultSiteName = $currentSettings['site_name'];
$defaultFooterText = $currentSettings['footer_text'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = trim($_POST['site_name']);
    $footerText = trim($_POST['footer_text']);

    // Update the settings in the database
    $stmt = $pdo->prepare("UPDATE settings SET site_name = ?, footer_text = ? WHERE id = ?");
    $stmt->execute([$siteName, $footerText, $currentSettings['id']]);

    $message = "✅ Settings updated successfully!";
    $defaultSiteName = $siteName;
    $defaultFooterText = $footerText;
}

include '../includes/header.php';
?>

<main class="container">
    <h2>⚙️ Site Settings</h2>

    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <section class="form-section">
        <form method="POST">
            <label for="site_name">Site Name:</label>
            <input type="text" name="site_name" id="site_name" value="<?php echo htmlspecialchars($defaultSiteName); ?>" required>

            <label for="footer_text">Footer Text:</label>
            <textarea name="footer_text" id="footer_text" required><?php echo htmlspecialchars($defaultFooterText); ?></textarea>

            <button type="submit" class="btn">💾 Save Settings</button>
        </form>
    </section>
</main>




<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fa;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 750px;
        margin: 30px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-size: 30px; /* Larger header font for emphasis */
        color: #333;
        margin-bottom: 25px;
    }

    .success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-size: 1.1rem; /* Slightly larger font for emphasis */
    }

    form label {
        display: block;
        margin-bottom: 12px; /* Increased space between label and input */
        font-weight: bold;
        color: #444;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 18px; /* Slightly larger font for better readability */
        background: #f9f9f9;
        transition: border 0.2s ease;
    }

    input[type="text"]:focus,
    textarea:focus {
        border-color: #1e90ff;
        outline: none;
        background: #fff;
        box-shadow: 0 0 5px rgba(30, 144, 255, 0.6); /* Subtle focus shadow */
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .btn {
        background-color: #1e90ff;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .btn:hover {
        background-color: #187bcd;
        transform: scale(1.05); /* Adds a subtle "zoom in" effect */
    }

    @media (max-width: 600px) {
        .container {
            padding: 20px;
            width: 90%; /* Ensures form takes up more of the available space */
        }

        .btn {
            width: 100%; /* Makes the button full width on smaller screens */
        }
    }
</style>
