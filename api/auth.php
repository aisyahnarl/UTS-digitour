<?php
include __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // Simpan ke cookie (berlaku 1 hari)
        $expire = time() + 86400;
        setcookie('user_id',   $user['id'],       $expire, '/');
        setcookie('user_name', $user['fullname'],  $expire, '/');
        setcookie('user_role', $user['role'],      $expire, '/');

        if ($user['role'] === 'admin') {
            header("Location: /manage_destinasi.php");
        } else {
            header("Location: /dashboard.php");
        }
        exit;

    } else {
        setcookie('login_error', 'Email atau password salah!', time() + 10, '/');
        header("Location: /login.php");
        exit;
    }
}

header("Location: /login.php");
exit;