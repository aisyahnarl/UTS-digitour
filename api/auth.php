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
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['fullname'];
        $_SESSION['role']    = $user['role'];

        // Deteksi base path otomatis
        $base = dirname($_SERVER['PHP_SELF']);
        $base = rtrim($base, '/');

        if ($user['role'] === 'admin') {
            header("Location: $base/manage_destinasi.php");
        } else {
            header("Location: $base/dashboard.php");
        }
        exit;

    } else {
        $_SESSION['error'] = 'Email atau password salah!';
        $base = dirname($_SERVER['PHP_SELF']);
        $base = rtrim($base, '/');
        header("Location: $base/login.php");
        exit;
    }
}

header("Location: login.php");
exit;