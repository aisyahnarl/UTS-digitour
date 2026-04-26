<?php
session_start();
// Proteksi login: Sama dengan file lainnya
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <title>Peta Real-Time - DigiTour</title>
    <style>
        /* Mengatur tinggi peta agar pas di dalam main content */
        #map { height: 70vh; width: 100%; border-radius: 40px; z-index: 1; }
        .leaflet-popup-content-wrapper { border-radius: 15px; padding: 5px; }
    </style>
</head>
<body class="bg-slate-50 font-sans">
    <div class="flex flex-col md:flex-row min-h-screen">
        <nav class="w-full md:w-72 bg-white border-r border-slate-200 p-6 flex flex-col justify-between">
            <div class="space-y-2">
                <div class="text-3xl font-black text-blue-600 mb-10 tracking-tighter italic">DigiTour<span class="text-orange-500">.</span></div>
                
                <a href="dashboard.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-all">
                    <span class="text-xl">🏠</span> <span class="font-bold">Dashboard</span>
                </a>
                
                <a href="tour.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-all">
                    <span class="text-xl">🎥</span> <span class="font-bold">Guide Library</span>
                </a>
                
                <a href="map_real.php" class="flex items-center gap-4 p-4 bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-200 transition-all">
                    <span class="text-xl">📍</span> <span class="font-bold">Peta Real-Time</span>
                </a>
            </div>

            <div class="pb-6">
                <a href="logout.php" class="flex items-center gap-4 p-4 text-red-500 hover:bg-red-50 rounded-2xl transition-all">
                    <span class="text-xl">🚪</span> <span class="font-bold">Keluar</span>
                </a>
            </div>
        </nav>

        <main class="flex-1 p-6 md:p-12 overflow-y-auto">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Eksplorasi Sekitarmu</h1>
                    <p class="text-slate-500">Lihat lokasi wisata terdekat secara real-time.</p>
                </div>
                <div class="hidden md:block">
                    <span class="text-xs bg-green-100 text-green-700 px-4 py-2 rounded-full font-black tracking-widest uppercase">GPS Online</span>
                </div>
            </header>

            <div class="bg-white p-4 rounded-[50px] shadow-sm border border-slate-100">
                <div id="map" class="shadow-inner"></div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-600 p-6 rounded-[32px] text-white">
                    <p class="font-bold opacity-80 mb-2">Lokasi Terdekat</p>
                    <h4 class="text-xl font-black">Candi Borobudur</h4>
                    <p class="text-sm opacity-90 mt-2">Dapatkan panduan suara otomatis saat Anda mendekati area candi.</p>
                </div>
                <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
                    <p class="font-bold text-slate-400 mb-2">Status GPS</p>
                    <h4 class="text-xl font-black text-slate-800">Akurasi Tinggi</h4>
                    <p class="text-sm text-slate-500 mt-2">Pastikan izin lokasi pada browser Anda selalu aktif.</p>
                </div>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi Peta
        var map = L.map('map').setView([-7.6079, 110.2038], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Marker Wisata
        var marker = L.marker([-7.6079, 110.2038]).addTo(map)
            .bindPopup(`
                <div class="text-center p-2">
                    <h3 class="font-bold text-lg">Candi Borobudur</h3>
                    <p class="text-sm text-gray-600 mb-2">Audio Guide Tersedia</p>
                    <a href="tour.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold">Buka Guide</a>
                </div>
            `);

        // Deteksi Lokasi
        map.locate({setView: true, maxZoom: 16});
        function onLocationFound(e) {
            var radius = e.accuracy / 2;
            L.circle(e.latlng, radius, {
                color: '#2563eb',
                fillColor: '#3b82f6',
                fillOpacity: 0.2
            }).addTo(map).bindPopup("Kamu berada di radius " + Math.round(radius) + " meter dari titik ini").openPopup();
        }
        map.on('locationfound', onLocationFound);
    </script>
</body>
</html>