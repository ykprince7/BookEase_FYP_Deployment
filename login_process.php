<?php
// login_process.php - Handles user login authentication

// Start session to manage user login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connections and essentials
$host = 'localhost';
$dbname = 'BookEase';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (isset($_POST['login-submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check for empty fields and redirect back with error
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=emptyfields");
        exit();
    }
    
    // Check if user exists and fetch user data
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 0) {
        header("Location: login.php?error=nouser");
        exit();
    }
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify password using password_verify
    if (!password_verify($password, $user['password'])) {
        header("Location: login.php?error=wrongpassword");
        exit();
    }
    
    // Login successful - set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_role'] = $user['role'];
    
    // Set remember me cookie if selected, remember me functionality can be implemented using a secure token stored in the database and a cookie on the client side. For simplicity, we will use a basic cookie here.
    if (isset($_POST['remember'])) {
        $cookie_name = "remember_me";
        $cookie_value = $user['id'] . ':' . hash('sha256', $user['password']);
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 30 days
    }
    
    // Redirect to dashboard or home page based on user role
    if ($user['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php?login=success");
    }
    exit();
    
} else {
    // If someone tries to access this page directly without submitting the form, redirect to login page
    header("Location: login.php");
    exit();
}
?>