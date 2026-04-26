<?php
include 'config.php';
session_start();

if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit;
}

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $email    = trim(strtolower(mysqli_real_escape_string($conn, $_POST['email'] ?? '')));
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email dan password wajib diisi.";
        header("Location: login.php");
        exit;
    }

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['fullname'];
        $_SESSION['role']    = trim(strtolower($user['role']));

        if ($_SESSION['role'] === 'admin') {
            header("Location: manage_destinasi.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "Email atau password salah.";
        header("Location: login.php");
        exit;
    }
}

header("Location: login.php");
exit;
?>