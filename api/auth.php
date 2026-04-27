<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/api/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /login.php");
    exit;
}

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['fullname'];
        $_SESSION['role']    = $user['role'];
        session_write_close();

        if ($user['role'] === 'admin') {
            header("Location: /manage_destinasi.php");
        } else {
            header("Location: /dashboard.php");
        }
        exit;
    } else {
        header("Location: /login.php?error=1");
        exit;
    }
} catch (PDOException $e) {
    header("Location: /login.php?error=2");
    exit;
}
?>