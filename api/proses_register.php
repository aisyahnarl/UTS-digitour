<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$fullname = trim(mysqli_real_escape_string($conn, $_POST['fullname'] ?? ''));
$email    = trim(strtolower(mysqli_real_escape_string($conn, $_POST['email'] ?? '')));
$password = trim($_POST['password'] ?? '');

// Validasi
$errors = [];

if (empty($fullname))                                               $errors[] = "Nama lengkap tidak boleh kosong.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))    $errors[] = "Format email tidak valid.";
if (strlen($password) < 8)                                          $errors[] = "Password minimal 8 karakter.";

// Cek email duplikat
if (empty($errors)) {
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $errors[] = "Email sudah terdaftar. Silakan login.";
    }
}

// Simpan ke DB
if (empty($errors)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql    = "INSERT INTO users (fullname, email, password, role) VALUES ('$fullname', '$email', '$hashed', 'user')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    } else {
        $errors[] = "Registrasi gagal: " . mysqli_error($conn);
    }
}

$_SESSION['error'] = implode('<br>', $errors);
header("Location: register.php");
exit;
?>