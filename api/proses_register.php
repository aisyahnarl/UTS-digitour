<?php
session_start();
include __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$fullname = trim(mysqli_real_escape_string($conn, $_POST['fullname']));
$email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
$password = $_POST['password'];

// Validasi tidak boleh kosong
if (empty($fullname) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'Semua field harus diisi!';
    header("Location: register.php");
    exit;
}

// Cek email sudah terdaftar
$cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
if (mysqli_num_rows($cek) > 0) {
    $_SESSION['error'] = 'Email sudah terdaftar, gunakan email lain!';
    header("Location: register.php");
    exit;
}

// Hash password
$hash = password_hash($password, PASSWORD_BCRYPT);

// Insert ke database
$sql = "INSERT INTO users (fullname, email, password, role) 
        VALUES ('$fullname', '$email', '$hash', 'user')";

if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = 'Akun berhasil dibuat! Silakan masuk.';
    header("Location: login.php");
    exit;
} else {
    $_SESSION['error'] = 'Gagal membuat akun: ' . mysqli_error($conn);
    header("Location: register.php");
    exit;
}