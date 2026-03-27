<!-- auth.php content placeholder -->
<?php
// includes/auth.php
session_start();

// Login function
function login($email, $password) {
    global $pdo;
    // Modify the query to look for the email instead of the username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check if the user exists and the password is correct
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];  // Or use $user['email'] if preferred
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

// Check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if the user is an admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
}
?>

