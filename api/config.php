<?php
$host = "gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com"; 
$port = 4000;
$user = "36Js8ra1yYJRUrz.root";    
$pass = "82CdEOFZFqBq1tXG";       
$db   = "digitour_db";        

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_init();
mysqli_ssl_set($conn, null, null, null, null, null);

$connected = mysqli_real_connect(
    $conn, $host, $user, $pass, $db, $port, null, MYSQLI_CLIENT_SSL
);

if (!$connected) {
    die(json_encode(['status' => 'error', 'message' => 'Koneksi gagal: ' . mysqli_connect_error()]));
}

mysqli_set_charset($conn, 'utf8mb4');

class DBSessionHandler implements SessionHandlerInterface {
    private $conn;
    public function __construct($conn) { $this->conn = $conn; }
    public function open($path, $name): bool { return true; }
    public function close(): bool { return true; }
    public function read($id): string {
        $id  = mysqli_real_escape_string($this->conn, $id);
        $res = mysqli_query($this->conn, "SELECT data FROM sessions WHERE id='$id'");
        $row = mysqli_fetch_assoc($res);
        return $row ? $row['data'] : '';
    }
    public function write($id, $data): bool {
        $id   = mysqli_real_escape_string($this->conn, $id);
        $data = mysqli_real_escape_string($this->conn, $data);
        $time = time();
        mysqli_query($this->conn, "REPLACE INTO sessions (id, data, last_activity) VALUES ('$id','$data','$time')");
        return true;
    }
    public function destroy($id): bool {
        $id = mysqli_real_escape_string($this->conn, $id);
        mysqli_query($this->conn, "DELETE FROM sessions WHERE id='$id'");
        return true;
    }
    public function gc($maxlifetime): int|false {
        mysqli_query($this->conn, "DELETE FROM sessions WHERE last_activity < " . (time() - $maxlifetime));
        return true;
    }
}

$handler = new DBSessionHandler($conn);
session_set_save_handler($handler, true);
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
session_start();
?>