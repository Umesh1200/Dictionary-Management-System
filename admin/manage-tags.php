<?php
require '../includes/config.php';
require '../includes/auth.php';
include '../includes/header.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$message = "";

// Handle Add Tag
if (isset($_POST['add_tag'])) {
    $tag = trim($_POST['tag']);
    if ($tag) {
        $stmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
        if ($stmt->execute([$tag])) {
            $message = "✅ Tag added successfully!";
        } else {
            $message = "❌ Error adding tag.";
        }
    } else {
        $message = "⚠️ Please enter a tag name.";
    }
}

// Handle Edit Tag
if (isset($_POST['edit_tag'])) {
    $tagId = $_POST['tag_id'];
    $newTagName = trim($_POST['new_tag_name']);

    if ($tagId && $newTagName) {
        $stmt = $pdo->prepare("UPDATE tags SET name = ? WHERE id = ?");
        if ($stmt->execute([$newTagName, $tagId])) {
            $message = "✏️ Tag updated successfully!";
        } else {
            $message = "❌ Error updating tag.";
        }
    } else {
        $message = "⚠️ Please enter a new tag name.";
    }
}

// Handle Delete Tag
if (isset($_GET['delete_tag'])) {
    $id = $_GET['delete_tag'];
    $stmt = $pdo->prepare("DELETE FROM tags WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "🗑️ Tag deleted.";
        header("Location: manage-tags.php"); // Redirect to avoid the message being stuck
        exit;
    } else {
        $message = "❌ Error deleting tag.";
    }
}

// Fetch all tags
$tags = $pdo->query("SELECT * FROM tags ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Tags</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        main.container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2, h3 {
            text-align: center;
            color: #2c3e50;
        }

        .success {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .btn {
            background: #3498db;
            color: #fff;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn:hover {
            background: #2980b9;
        }

        .table-section {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            border-bottom: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #ecf0f1;
            color: #333;
        }

        .btn-small {
            padding: 6px 10px;
            font-size: 0.9em;
            border-radius: 4px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
        }

        .btn-small:hover {
            background: #c0392b;
        }

        .red {
            background-color: #e74c3c;
        }

        .edit-form {
            display: flex;
            gap: 5px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .edit-section {
            display: none;
        }

        .show-edit-btn {
            background: #f39c12;
            color: white;
            padding: 6px 10px;
            font-size: 0.9em;
            border-radius: 4px;
            cursor: pointer;
        }

        .show-edit-btn:hover {
            background: #e67e22;
        }

        @media (max-width: 600px) {
            form {
                flex-direction: column;
                align-items: center;
            }

            input[type="text"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<main class="container">
    <h2>📁 Manage Tags</h2>

    <!-- Display the message after an action (add, edit, delete) -->
    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Add Tag Form -->
    <section class="form-section">
        <h3>Add New Tag</h3>
        <form method="POST">
            <input type="text" name="tag" placeholder="Tag Name" required>
            <button type="submit" name="add_tag" class="btn">➕ Add</button>
        </form>
    </section>

    <!-- Tag List -->
    <section class="table-section">
        <h3>Existing Tags</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tag Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tags as $tag): ?>
                    <tr>
                        <td><?php echo $tag['id']; ?></td>
                        <td id="name-<?php echo $tag['id']; ?>"><?php echo htmlspecialchars($tag['name']); ?></td>
                        <td>
                            <!-- Edit Form with Toggle -->
                            <button class="show-edit-btn" onclick="toggleEditForm(<?php echo $tag['id']; ?>)">✏️ Edit</button>
                            <form method="POST" class="edit-form edit-section" id="edit-form-<?php echo $tag['id']; ?>">
                                <input type="hidden" name="tag_id" value="<?php echo $tag['id']; ?>">
                                <input type="text" name="new_tag_name" placeholder="New Name" required>
                                <button type="submit" name="edit_tag" class="btn">✏️ Save</button>
                            </form>
                            
                            <!-- Delete Button -->
                            <a href="manage-tags.php?delete_tag=<?php echo $tag['id']; ?>" class="btn-small" onclick="return confirm('Delete this tag?')">🗑 Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<script>
    function toggleEditForm(tagId) {
        const editForm = document.getElementById('edit-form-' + tagId);
        const showEditButton = document.querySelector('[onclick="toggleEditForm(' + tagId + ')"]');
        const deleteButton = document.querySelector('[href="manage-tags.php?delete_tag=' + tagId + '"]');

        // Toggle the visibility of the edit form and buttons
        if (editForm.style.display === 'none' || editForm.style.display === '') {
            editForm.style.display = 'flex';  // Show edit form
            showEditButton.style.display = 'none';  // Hide edit button
            deleteButton.style.display = 'none';  // Hide delete button
        } else {
            editForm.style.display = 'none';  // Hide edit form
            showEditButton.style.display = 'inline-block';  // Show edit button again
            deleteButton.style.display = 'inline-block';  // Show delete button again
        }
    }
</script>

</body>
</html>
