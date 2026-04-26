<?php
include __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit;
}

$fullname = trim(mysqli_real_escape_string($conn, $_POST['fullname']));
$email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
$password = $_POST['password'];

if (empty($fullname) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'Semua field harus diisi!';
    session_write_close();
    header("Location: register.php");
    exit;
}

$cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
if (mysqli_num_rows($cek) > 0) {
    $_SESSION['error'] = 'Email sudah terdaftar!';
    session_write_close();
    header("Location: register.php");
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$sql  = "INSERT INTO users (fullname, email, password, role) VALUES ('$fullname','$email','$hash','user')";

if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = 'Akun berhasil dibuat! Silakan masuk.';
    session_write_close();
    header("Location: login.php");
    exit;
} else {
    $_SESSION['error'] = 'Gagal: ' . mysqli_error($conn);
    session_write_close();
    header("Location: register.php");
    exit;
}
?>