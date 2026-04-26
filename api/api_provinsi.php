<?php
// Set header agar output dikenali sebagai JSON oleh frontend
header('Content-Type: application/json');

// (Opsional) Buka baris di bawah ini jika frontend dan backend kamu beda port/domain (CORS)
// header("Access-Control-Allow-Origin: *"); 

// 1. Masukkan API Key rahasia kamu di sini
$apiKey = '317d3d127dfae5dcd6eae3998f5c4d3f';

// 2. Tentukan Endpoint URL BPS untuk mengambil data Provinsi
$url = "https://webapi.bps.go.id/v1/api/domain/type/prov/key/{$apiKey}/";

// 3. Inisiasi cURL (Library PHP untuk HTTP Request)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Catatan: Jika kamu mencoba ini di localhost (XAMPP/Laragon) dan error masalah SSL, 
// aktifkan baris di bawah ini. Namun untuk di server production (hosting), sebaiknya dihapus.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 4. Eksekusi cURL dan simpan responnya
$response = curl_exec($ch);

// 5. Cek apakah ada error saat menembak API
if (curl_errno($ch)) {
    // Jika gagal, kirim pesan error ke frontend
    echo json_encode([
        'status' => 'error',
        'message' => curl_error($ch)
    ]);
} else {
    // Jika sukses, langsung teruskan data JSON murni dari BPS ke frontend
    echo $response;
}

// 6. Tutup koneksi cURL
curl_close($ch);
?>