<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}
include '../includes/header.php';
$message = "";

// Handle Add Category
if (isset($_POST['add_category'])) {
    $category = trim($_POST['category']);
    if ($category) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$category])) {
            $message = "✅ Category added successfully!";
        } else {
            $message = "❌ Error adding category.";
        }
    } else {
        $message = "⚠️ Please enter a category name.";
    }
}

// Handle Edit Category
if (isset($_POST['edit_category'])) {
    $categoryId = $_POST['category_id'];
    $newCategoryName = trim($_POST['new_category_name']);

    if ($categoryId && $newCategoryName) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        if ($stmt->execute([$newCategoryName, $categoryId])) {
            $message = "✏️ Category updated successfully!";
        } else {
            $message = "❌ Error updating category.";
        }
    } else {
        $message = "⚠️ Please enter a new category name.";
    }
}

// Handle Delete Category
if (isset($_GET['delete_category'])) {
    $id = $_GET['delete_category'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "🗑️ Category deleted.";
        header("Location: manage-categories.php"); // Redirect to avoid the message being stuck
        exit;
    } else {
        $message = "❌ Error deleting category.";
    }
}

// Fetch all categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
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
    <h2>📁 Manage Categories</h2>

    <!-- Display the message after an action (add, edit, delete) -->
    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Add Category Form -->
    <section class="form-section">
        <h3>Add New Category</h3>
        <form method="POST">
            <input type="text" name="category" placeholder="Category Name" required>
            <button type="submit" name="add_category" class="btn">➕ Add</button>
        </form>
    </section>

    <!-- Category List -->
    <section class="table-section">
        <h3>Existing Categories</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td id="name-<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <!-- Edit Form with Toggle -->
                            <button class="show-edit-btn" onclick="toggleEditForm(<?php echo $category['id']; ?>)">✏️ Edit</button>
                            <form method="POST" class="edit-form edit-section" id="edit-form-<?php echo $category['id']; ?>">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <input type="text" name="new_category_name" placeholder="New Name" required>
                                <button type="submit" name="edit_category" class="btn">✏️ Save</button>
                            </form>
                            
                            <!-- Delete Button -->
                            <a href="manage-categories.php?delete_category=<?php echo $category['id']; ?>" class="btn-small" onclick="return confirm('Delete this category?')">🗑 Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<script>
    function toggleEditForm(categoryId) {
        const editForm = document.getElementById('edit-form-' + categoryId);
        const showEditButton = document.querySelector('[onclick="toggleEditForm(' + categoryId + ')"]');
        const deleteButton = document.querySelector('[href="manage-categories.php?delete_category=' + categoryId + '"]');

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
