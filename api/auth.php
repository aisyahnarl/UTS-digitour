<?php
include __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /login.php");
    exit;
}

$email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
$password = $_POST['password'];

$sql    = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user   = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {

    // Cookie dengan Secure=true (wajib untuk HTTPS/Vercel)
    $expire = time() + 86400;
    setcookie('user_id',   $user['id'],       $expire, '/', '', true, true);
    setcookie('user_name', $user['fullname'],  $expire, '/', '', true, true);
    setcookie('user_role', $user['role'],      $expire, '/', '', true, true);

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