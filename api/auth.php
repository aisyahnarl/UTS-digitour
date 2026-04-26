<?php
session_start();
include __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'login') {
    
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user   = mysqli_fetch_assoc($result);

    // ── DEBUG SEMENTARA ── hapus setelah berhasil
    echo "<pre>";
    echo "Email dicari: " . $email . "\n";
    echo "User ditemukan: "; var_dump($user);
    if ($user) {
        echo "Password verify: "; var_dump(password_verify($password, $user['password']));
    }
    echo "</pre>";
    die(); // stop dulu biar bisa baca hasilnya
    // ── END DEBUG ──

    if ($user && password_verify($password, $user['password'])) {
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
        header("Location: login.php");
        exit;
    }
}

header("Location: login.php");
exit;