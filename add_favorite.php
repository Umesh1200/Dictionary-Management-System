<?php
require 'includes/config.php';
require 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$wordId = isset($_POST['word_id']) ? (int)$_POST['word_id'] : 0;

// Check if the word is already in favorites
$checkStmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND word_id = ?");
$checkStmt->execute([$userId, $wordId]);

if ($checkStmt->fetch()) {
    // Redirect with message if already favorited
    header("Location: word-detail.php?id=$wordId&msg=already_favorited");
    exit;
}

// Insert into favorites
$stmt = $pdo->prepare("INSERT INTO favorites (user_id, word_id) VALUES (?, ?)");
$stmt->execute([$userId, $wordId]);

// Redirect back to the word details page
header("Location: word-detail.php?id=$wordId&msg=favorited");
exit;
?>
