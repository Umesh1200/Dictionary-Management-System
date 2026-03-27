<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$message = "";

// Fetch Categories and Tags for the dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$tags = $pdo->query("SELECT * FROM tags")->fetchAll();

// Handle Import
if (isset($_POST['import'])) {
    if ($_FILES['file']['name']) {
        $uploadDir = __DIR__ . '/../uploads/'; // Absolute path to the uploads folder
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create the uploads directory if it doesn't exist
        }

        $filePath = $uploadDir . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            $message = "✅ Words imported successfully!";
            
            // Parse CSV and insert data
            $handle = fopen($filePath, 'r');
            $header = fgetcsv($handle); // Read header line
            while (($data = fgetcsv($handle)) !== false) {
                // Assuming the CSV has columns: Word, Definition, Category, Tags
                $word = $data[0];
                $definition = $data[1];
                $category = $data[2];
                $tags = explode(',', $data[3]); // Assuming tags are comma-separated

                // Check if the word already exists in the words table
                $wordStmt = $pdo->prepare("SELECT id FROM words WHERE word = ?");
                $wordStmt->execute([$word]);
                $existingWord = $wordStmt->fetch();

                if (!$existingWord) {
                    // Insert word into the words table if it doesn't exist
                    $stmt = $pdo->prepare("INSERT INTO words (word, definition) VALUES (?, ?)");
                    $stmt->execute([$word, $definition]);
                    $wordId = $pdo->lastInsertId(); // Get the last inserted word ID

                    // Insert category if applicable
                    if ($category) {
                        // Check if the category exists in the categories table
                        $categoryStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
                        $categoryStmt->execute([$category]);
                        $categoryRow = $categoryStmt->fetch();
                        
                        if (!$categoryRow) {
                            // If category doesn't exist, insert it into the categories table
                            $insertCategoryStmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                            $insertCategoryStmt->execute([$category]);
                            $categoryId = $pdo->lastInsertId(); // Get the ID of the newly inserted category
                        } else {
                            // If category exists, use its ID
                            $categoryId = $categoryRow['id'];
                        }

                        // Insert the word_category relationship
                        $categoryStmt = $pdo->prepare("INSERT INTO word_categories (word_id, category_id) VALUES (?, ?)");
                        $categoryStmt->execute([$wordId, $categoryId]);
                    }

                    // Insert tags if applicable
                    foreach ($tags as $tag) {
                        $tag = trim($tag); // Remove any spaces
                        
                        // Check if the tag already exists in the 'tags' table
                        $tagStmt = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
                        $tagStmt->execute([$tag]);
                        $tagRow = $tagStmt->fetch();
                        
                        if (!$tagRow) {
                            // If tag doesn't exist, insert it into the tags table
                            $insertTagStmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
                            $insertTagStmt->execute([$tag]);
                            $tagId = $pdo->lastInsertId(); // Get the ID of the newly inserted tag
                        } else {
                            // If tag exists, use its ID
                            $tagId = $tagRow['id'];
                        }

                        // Insert the word_tag relationship
                        $tagStmt = $pdo->prepare("INSERT INTO word_tags (word_id, tag_id) VALUES (?, ?)");
                        $tagStmt->execute([$wordId, $tagId]);
                    }
                } else {
                    // Word already exists, skip it
                    $message = "⚠️ Word '{$word}' already exists in the dictionary. Skipping.";
                }
            }
            fclose($handle);
        } else {
            $message = "⚠️ Failed to upload file.";
        }
    } else {
        $message = "⚠️ Please select a file to upload.";
    }
}

// Handle Export
if (isset($_POST['export'])) {
    $words = $pdo->query("SELECT * FROM words")->fetchAll();
    $filename = "words_export_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);
    $fp = fopen('php://output', 'w');
    fputcsv($fp, ['ID', 'Word', 'Definition']);
    foreach ($words as $word) {
        fputcsv($fp, [$word['id'], $word['word'], $word['definition']]);
    }
    fclose($fp);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import / Export - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        header {
            background: #283e4a;
            color: white;
            padding: 1rem 2rem;
            text-align: center;
        }
        main.container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #283e4a;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section h3 {
            margin-bottom: 10px;
            color: #444;
        }
        input[type="file"], select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            width: 100%;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        p.success {
            background: #e0f9e7;
            color: #2e7d32;
            padding: 10px 15px;
            border-left: 5px solid #2e7d32;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<header>
    <h1>📘 Dictionary Admin - Import / Export</h1>
</header>

<main class="container">
    <h2>Import / Export Words</h2>

    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <section class="form-section">
        <h3>📥 Import Words</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <br>
            <button type="submit" name="import" class="btn">Import CSV</button>
        </form>
    </section>

    <section class="form-section">
        <h3>📤 Export Words</h3>
        <form method="POST">
            <button type="submit" name="export" class="btn">Export CSV</button>
        </form>
    </section>
</main>
</body>
</html>
