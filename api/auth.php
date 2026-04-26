<?php
session_start();
include __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['fullname'];
        $_SESSION['role']    = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: manage_destinasi.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;

    } else {
        $_SESSION['error'] = 'Email atau password salah!';
        header("Location: /login.php");
        exit;
    }
}

// Jika akses langsung tanpa POST
header("Location: /login.php");
exit;