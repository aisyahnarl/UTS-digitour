<?php
header('Content-Type: application/json');

// Tangkap ID Provinsi yang dikirim oleh Javascript
$prov_id = isset($_GET['prov_id']) ? $_GET['prov_id'] : '';

if (empty($prov_id)) {
    echo json_encode(['error' => 'ID Provinsi tidak ditemukan']);
    exit;
}

// 1. Masukkan API Key kamu di sini
$apiKey = '317d3d127dfae5dcd6eae3998f5c4d3f'; // Gunakan API Key BPS milikmu

// 2. Tentukan Endpoint URL BPS untuk mengambil data Kabupaten berdasarkan Provinsi
$url = "https://webapi.bps.go.id/v1/api/domain/type/kabbyprov/prov/{$prov_id}/key/{$apiKey}/";

// 3. Inisiasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 4. Eksekusi cURL
$response = curl_exec($ch);

// 5. Output
if (curl_errno($ch)) {
    echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
} else {
    echo $response;
}

curl_close($ch);
?>