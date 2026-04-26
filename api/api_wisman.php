<?php
/**
 * api_wisman.php — v2 (fixed key decoder)
 *
 * ANALISIS FORMAT KEY DATACONTENT BPS (dari JSON nyata):
 *   "245147001261" = Grand Total Jan 2026 → 1.188.420
 *   "245147001262" = Grand Total Feb 2026 → 1.159.690
 *   "1147001261"   = Malaysia Jan         → 211.174
 *   "1147001262"   = Malaysia Feb         → 199.216
 *   "13147001261"  = Tiongkok Jan         → 110.973
 *   "13147001262"  = Tiongkok Feb         → 150.822
 *
 *   Rumus: {vervar_id} + "14700126" + {turtahun_val}
 *          "14700126" = var(1470) + "0" + "1" + tahun_2digit(26)
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

define('BPS_API_KEY', '317d3d127dfae5dcd6eae3998f5c4d3f');
define('BPS_BASE_URL', 'https://webapi.bps.go.id/v1/api/list');
define('BPS_VAR',    '1470');
define('BPS_DOMAIN', '0000');
define('BPS_TAHUN',  '126');

define('BULAN_AKTIF', [1, 2]);  // 1=Januari, 2=Februari

$TOTAL_IDS = [12, 36, 53, 112, 160, 183, 244, 245];

function kirimError(string $msg, int $code = 500): void {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// KEY FORMAT VERIFIED: {vervar} . "14700126" . {turtahun}
function buatKey(int $vervarId, int $bulan): string {
    return (string)$vervarId . '14700126' . (string)$bulan;
}

$apiUrl = sprintf(
    '%s/model/data/lang/ind/domain/%s/var/%s/th/%s/key/%s',
    BPS_BASE_URL, BPS_DOMAIN, BPS_VAR, BPS_TAHUN, BPS_API_KEY
);

$ctx = stream_context_create([
    'http' => [
        'method'        => 'GET',
        'timeout'       => 15,
        'ignore_errors' => true,
        'header'        => ['Accept: application/json', 'User-Agent: DigiTour/2.0'],
    ],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);

$raw = @file_get_contents($apiUrl, false, $ctx);
if ($raw === false) kirimError('Gagal terhubung ke BPS API.');

$bps = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) kirimError('Respons BPS bukan JSON valid.');
if (($bps['status'] ?? '') !== 'OK')       kirimError('BPS API status: ' . ($bps['status'] ?? 'unknown'));
if (empty($bps['datacontent']))             kirimError('datacontent kosong dari BPS API.');

$mapKebangsaan = [];
foreach ($bps['vervar'] as $item) {
    $mapKebangsaan[(int)$item['val']] = strip_tags($item['label']);
}

$mapBulan = [];
foreach (($bps['turtahun'] ?? []) as $b) {
    $mapBulan[(int)$b['val']] = $b['label'];
}

$tahunLabel = $bps['tahun'][0]['label'] ?? '2026';
$dc         = $bps['datacontent'];

// Per Bulan (Grand Total = vervar 245)
$pBulanLabels = [];
$pBulanValues = [];
foreach (BULAN_AKTIF as $bln) {
    $key            = buatKey(245, $bln);
    $pBulanLabels[] = $mapBulan[$bln] ?? "Bulan $bln";
    $pBulanValues[] = isset($dc[$key]) ? (int)$dc[$key] : 0;
}

// Per Kebangsaan
$kebangsaanTotal = [];
foreach ($mapKebangsaan as $vId => $vLabel) {
    if (in_array($vId, $TOTAL_IDS, true)) continue;
    $total = 0;
    foreach (BULAN_AKTIF as $bln) {
        $k      = buatKey($vId, $bln);
        $total += isset($dc[$k]) ? (int)$dc[$k] : 0;
    }
    if ($total > 0) $kebangsaanTotal[$vLabel] = $total;
}
arsort($kebangsaanTotal);
$top10 = array_slice($kebangsaanTotal, 0, 10, true);

// Ringkasan
$grandTotal     = array_sum($pBulanValues);
$maxVal         = !empty($pBulanValues) ? max($pBulanValues) : 0;
$maxIdx         = $maxVal > 0 ? (int)array_search($maxVal, $pBulanValues) : 0;
$bulanTertinggi = $pBulanLabels[$maxIdx] ?? '-';
$rataRata       = count($pBulanValues) > 0 ? $grandTotal / count($pBulanValues) : 0;

echo json_encode([
    'status'            => 'success',
    'tahun'             => $tahunLabel,
    'bulan_ditampilkan' => array_map(fn($b) => $mapBulan[$b] ?? "Bulan $b", BULAN_AKTIF),
    'per_bulan' => [
        'labels' => $pBulanLabels,
        'values' => $pBulanValues,
    ],
    'per_kebangsaan' => [
        'labels' => array_keys($top10),
        'values' => array_values($top10),
    ],
    'ringkasan' => [
        'grand_total'     => $grandTotal,
        'nilai_tertinggi' => $maxVal,
        'bulan_tertinggi' => $bulanTertinggi,
        'rata_rata_bulan' => (int)round($rataRata),
    ],
    'meta' => [
        'sumber'             => 'Badan Pusat Statistik (BPS)',
        'variabel'           => 'Wisatawan Mancanegara per Bulan Menurut Kebangsaan',
        'diambil_pada'       => date('Y-m-d H:i:s'),
        // Debug keys — hapus setelah data tampil benar
        'debug_key_jan'      => buatKey(245, 1),
        'debug_key_feb'      => buatKey(245, 2),
        'debug_val_jan'      => $dc[buatKey(245,1)] ?? 'NOT_FOUND',
        'debug_val_feb'      => $dc[buatKey(245,2)] ?? 'NOT_FOUND',
    ],
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);