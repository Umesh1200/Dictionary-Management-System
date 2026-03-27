<?php
require '../includes/config.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Handle deletion of search history item
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM search_history WHERE id = ? AND user_id = ?");
    $stmt->execute([$deleteId, $userId]);
    header("Location: history.php"); // Redirect after deletion
    exit;
}

// Fetch search history
$history = $pdo->prepare("SELECT * FROM search_history WHERE user_id = ? ORDER BY date_searched DESC");
$history->execute([$userId]);
$searchHistory = $history->fetchAll();

include '../includes/header.php';
?>

<main class="container">
    <h2>Search History</h2>

    <?php if (empty($searchHistory)): ?>
        <p>No search history found.</p>
    <?php else: ?>
        <section class="table-section">
            <table class="table">
                <thead>
                    <tr>
                        <th>Search Term</th>
                        <th>Date Searched</th>
                        <th>Action</th> <!-- Added Action column for delete button -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchHistory as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['search_term']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($item['date_searched'])); ?></td>
                            <td>
                                <!-- Delete button -->
                                <a href="history.php?delete_id=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this search term?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
<style>
    /* Page Styling */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        color: #333;
        font-size: 1.8rem;
        margin-bottom: 15px;
    }

    .table-section {
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #e9ecef;
    }

    p {
        font-size: 1.1rem;
        color: #6c757d;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.2s ease;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }
</style>
