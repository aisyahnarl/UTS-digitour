<?php
include __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method tidak valid']);
    exit;
}

$email    = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
$user   = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    setcookie('user_id',  $user['id'],       time()+86400, '/', '', true, false);
    setcookie('username', $user['fullname'], time()+86400, '/', '', true, false);
    setcookie('userrole', $user['role'],     time()+86400, '/', '', true, false);

    echo json_encode([
        'status'   => 'success',
        'redirect' => $user['role'] === 'admin' ? '/manage_destinasi.php' : '/dashboard.php'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email atau password salah!']);
}
?>