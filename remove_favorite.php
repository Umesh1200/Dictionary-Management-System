<?php
require 'includes/config.php';
require 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$wordId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete the word from favorites
$stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND word_id = ?");
$stmt->execute([$userId, $wordId]);

// Redirect back to the favorites page
header("Location: favorites.php?msg=removed");
exit;
?>
