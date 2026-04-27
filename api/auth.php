<?php
/**
 * auth.php — Proses login
 * Letakkan di: /api/auth.php
 *
 * PENTING: Tidak perlu session_start() di sini karena
 * config.php sudah menjalankannya via DBSessionHandler
 */
include __DIR__ . '/config.php'; // ← sudah include session_start()

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header("Location: /login.php?error=1");
    exit;
}

// Pakai MySQLi (konsisten dengan config.php)
$emailSafe = mysqli_real_escape_string($conn, $email);
$result    = mysqli_query($conn, "SELECT * FROM users WHERE email='$emailSafe' LIMIT 1");

if (!$result) {
    // Query gagal
    header("Location: /login.php?error=2");
    exit;
}

$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    // Login berhasil — simpan ke SESSION
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['fullname'];
    $_SESSION['role']    = $user['role'];
    session_write_close();

    if ($user['role'] === 'admin') {
        header("Location: /manage_destinasi.php");
    } else {
        header("Location: /dashboard.php");
    }
    exit;

} else {
    // Login gagal
    header("Location: /login.php?error=1");
    exit;
}
?>