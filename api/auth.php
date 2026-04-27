<?php
include $_SERVER['DOCUMENT_ROOT'] . '/api/config.php';

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        session_start();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name']    = $row['fullname'];
        $_SESSION['role']    = $row['role'];
        session_write_close();

        if ($row['role'] === 'admin') {
            header("Location: /manage_destinasi.php");
        } else {
            header("Location: /dashboard.php");
        }
        exit;
    } else {
        header("Location: /login.php?error=1");
        exit;
    }
} catch (PDOException $e) {
    header("Location: /login.php?error=2");
    exit;
}
?>