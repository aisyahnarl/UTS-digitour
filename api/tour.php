<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit;
}

include 'config.php';

// ─── Filter kategori dari URL ─────────────────────────────────────────────────
$kategori = isset($_GET['kategori']) ? strtolower(trim($_GET['kategori'])) : 'semua';
$kategoriValid = ['semua', 'candi', 'alam', 'kuliner', 'religi'];
if (!in_array($kategori, $kategoriValid)) $kategori = 'semua';

// ─── Data statis fallback ─────────────────────────────────────────────────────
$semuaDestinasi = [
    [
        'nama'        => 'Candi Borobudur',
        'kategori'    => 'candi',
        'deskripsi'   => 'Jelajahi relief Kamadhatu melalui panduan audio spasial eksklusif kami.',
        'deskripsi_panjang' => 'Candi Borobudur adalah monumen Buddha terbesar di dunia yang terletak di Magelang, Jawa Tengah, Indonesia. Dibangun pada abad ke-8 dan ke-9 Masehi pada masa pemerintahan Dinasti Syailendra, candi ini merupakan salah satu keajaiban arsitektur kuno yang menakjubkan. Borobudur terdiri dari sembilan platform bertumpuk — enam berbentuk persegi dan tiga melingkar — dihiasi oleh 2.672 panel relief dan 504 arca Buddha. Di puncaknya terdapat stupa utama yang dikelilingi oleh 72 stupa berlubang, masing-masing berisi arca Buddha dalam posisi meditasi. Situs ini menjadi tujuan ziarah umat Buddha dari seluruh dunia dan ditetapkan sebagai Warisan Dunia UNESCO sejak tahun 1991.',
        'foto'        => 'assets/candi.jpg',
        'video_id'    => 'Lv_GojoT1v4',
        'badge'       => 'Terpopuler',
        'badge_color' => 'text-blue-600',
        'durasi'      => '04:20',
        'progress'    => 'w-1/3',
        'lokasi'      => 'Magelang, Jawa Tengah',
        'jam_buka'    => '06:00 - 17:00 WIB',
    ],
    [
        'nama'        => 'Candi Prambanan',
        'kategori'    => 'candi',
        'deskripsi'   => 'Dengarkan kisah epik Roro Jonggrang dalam format narasi cinematic audio.',
        'deskripsi_panjang' => 'Candi Prambanan adalah kompleks candi Hindu terbesar di Indonesia dan salah satu yang terbesar di Asia Tenggara. Terletak di perbatasan Jawa Tengah dan Daerah Istimewa Yogyakarta, candi ini dibangun pada sekitar abad ke-9 Masehi oleh Kerajaan Mataram Kuno. Kompleks ini dipersembahkan untuk Trimurti — tiga dewa utama Hindu: Brahma (pencipta), Wisnu (pemelihara), dan Siwa (pemusnah). Candi utama Siwa memiliki ketinggian mencapai 47 meter dan dihiasi relief indah yang menceritakan kisah Ramayana. Legenda setempat mengenal candi ini sebagai Roro Jonggrang, seorang putri yang dikutuk menjadi arca karena menolak lamaran Bandung Bondowoso. Prambanan ditetapkan sebagai Warisan Dunia UNESCO pada tahun 1991.',
        'foto'        => 'assets/prambanan.jpg',
        'video_id'    => 'lYcv3czNNUA',
        'badge'       => 'Terbaru',
        'badge_color' => 'text-orange-600',
        'durasi'      => '06:15',
        'progress'    => '',
        'lokasi'      => 'Sleman, DI Yogyakarta',
        'jam_buka'    => '06:00 - 17:00 WIB',
    ],
    [
        'nama'        => 'Taman Nasional Komodo',
        'kategori'    => 'alam',
        'deskripsi'   => 'Temukan keajaiban alam Komodo melalui panduan audio eksklusif bersama ranger asli.',
        'deskripsi_panjang' => 'Taman Nasional Komodo adalah kawasan konservasi alam yang terletak di antara Pulau Sumbawa dan Flores, Nusa Tenggara Timur. Didirikan pada tahun 1980 dan ditetapkan sebagai Warisan Dunia UNESCO pada tahun 1991, taman ini merupakan satu-satunya habitat alami komodo (Varanus komodoensis) — kadal terbesar di dunia yang dapat tumbuh hingga 3 meter dan berbobot 70 kg. Selain komodo, taman ini juga menawarkan keindahan bawah laut yang luar biasa dengan terumbu karang, ikan pari manta, hiu, dan beragam biota laut lainnya. Pulau Padar yang ikonik dengan pemandangan bukit-bukit hijau dan teluk berwarna biru menjadi salah satu spot foto paling populer di Indonesia.',
        'foto'        => 'assets/pulau_komodo.jpg',
        'video_id'    => 'B95MRLYcazM',
        'badge'       => 'Unggulan',
        'badge_color' => 'text-green-600',
        'durasi'      => '05:30',
        'progress'    => 'w-1/2',
        'lokasi'      => 'Nusa Tenggara Timur',
        'jam_buka'    => '08:00 - 16:00 WITA',
    ],
    [
        'nama'        => 'Raja Ampat',
        'kategori'    => 'alam',
        'deskripsi'   => 'Selami keindahan bawah laut Raja Ampat bersama panduan audio eksklusif kami.',
        'deskripsi_panjang' => 'Raja Ampat adalah kepulauan yang terdiri dari lebih dari 1.500 pulau kecil, beting, dan gosong di ujung barat laut Papua, Indonesia. Nama Raja Ampat berasal dari legenda empat raja yang menguasai pulau-pulau utama: Waigeo, Salawati, Batanta, dan Misool. Kawasan ini diakui sebagai salah satu destinasi selam terbaik di dunia, dengan keanekaragaman hayati laut yang luar biasa — lebih dari 1.500 spesies ikan dan 600 spesies karang telah teridentifikasi di sini. Di atas permukaan, pemandangan karst yang dramatis, hutan hujan tropis, dan laguna berwarna pirus menciptakan lanskap yang memesona. Raja Ampat juga menjadi rumah bagi burung Cenderawasih yang terkenal dengan bulu-bulunya yang indah.',
        'foto'        => 'assets/raja_ampat.jpg',
        'video_id'    => 's--hVrm_LnI',
        'badge'       => 'Terbaru',
        'badge_color' => 'text-teal-600',
        'durasi'      => '07:45',
        'progress'    => 'w-1/4',
        'lokasi'      => 'Papua Barat Daya',
        'jam_buka'    => 'Buka 24 Jam',
    ],
];

// ─── Ambil dari DB ────────────────────────────────────────────────────────────
$dataDB = [];
if (isset($conn)) {
    if ($kategori === 'semua') {
        $sql = "SELECT * FROM destinasi ORDER BY id_destinasi DESC";
    } else {
        $kat = mysqli_real_escape_string($conn, ucfirst($kategori));
        $sql = "SELECT * FROM destinasi WHERE LOWER(kategori) = LOWER('$kat') ORDER BY id_destinasi DESC";
    }
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $dataDB[] = [
                'nama'              => $row['nama_wisata'],
                'kategori'          => strtolower($row['kategori']),
                'deskripsi'         => $row['deskripsi'],
                'deskripsi_panjang' => $row['deskripsi'],
                'foto'              => !empty($row['foto']) ? $row['foto'] : 'assets/candi.jpg',
                'video_id'          => '',
                'badge'             => 'Destinasi',
                'badge_color'       => 'text-blue-600',
                'durasi'            => '05:00',
                'progress'          => 'w-1/4',
                'lokasi'            => '-',
                'jam_buka'          => '-',
            ];
        }
    }
}

// ─── Filter statis & gabung ───────────────────────────────────────────────────
$dataStatis = array_values(array_filter($semuaDestinasi, function($d) use ($kategori) {
    return $kategori === 'semua' || $d['kategori'] === $kategori;
}));

$namaDB = array_column($dataDB, 'nama');
$dataSatisTambahan = array_values(array_filter($dataStatis, function($d) use ($namaDB) {
    return !in_array($d['nama'], $namaDB);
}));

$listDestinasi = array_merge($dataDB, $dataSatisTambahan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Guide Library - DigiTour</title>
    <style>
        .modal-backdrop { backdrop-filter: blur(8px); }
        .modal-enter { animation: modalIn 0.35s cubic-bezier(.175,.885,.32,1.275) forwards; }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.85) translateY(30px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-exit { animation: modalOut 0.2s ease-in forwards; }
        @keyframes modalOut {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0; transform: scale(0.9); }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans">
<div class="flex flex-col md:flex-row min-h-screen">

    <!-- ════ SIDEBAR ════ -->
    <nav class="w-full md:w-72 bg-white border-r border-slate-200 p-6 flex flex-col justify-between">
        <div class="space-y-2">
            <div class="text-3xl font-black text-blue-600 mb-10 tracking-tighter italic">DigiTour<span class="text-orange-500">.</span></div>
            <a href="dashboard.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-all duration-300">
                <span class="text-xl">🏠</span> <span class="font-bold">Dashboard</span>
            </a>
            <a href="tour.php" class="flex items-center gap-4 p-4 bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-200 transition-all">
                <span class="text-xl">🎥</span> <span class="font-bold">Guide Library</span>
            </a>
            <a href="map_real.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-all">
                <span class="text-xl">📍</span> <span class="font-bold">Peta Real-Time</span>
            </a>
        </div>
        <div class="pb-6">
            <a href="logout.php" class="flex items-center gap-4 p-4 text-red-500 hover:bg-red-50 rounded-2xl transition-all">
                <span class="text-xl">🚪</span> <span class="font-bold">Keluar Akun</span>
            </a>
        </div>
    </nav>

    <!-- ════ MAIN CONTENT ════ -->
    <main class="flex-1 p-6 md:p-12 overflow-y-auto">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Perpustakaan Panduan</h1>
                <p class="text-slate-500 mt-1">Pilih destinasi dan dengarkan cerita sejarahnya.</p>
            </div>
            <div class="flex gap-2">
                <a href="tour.php?kategori=semua"
                   class="px-5 py-2 rounded-full text-sm font-bold transition <?= $kategori === 'semua' ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-400' ?>">
                    Semua
                </a>
                <a href="tour.php?kategori=candi"
                   class="px-5 py-2 rounded-full text-sm font-bold transition <?= $kategori === 'candi' ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-400' ?>">
                    Candi
                </a>
                <a href="tour.php?kategori=alam"
                   class="px-5 py-2 rounded-full text-sm font-bold transition <?= $kategori === 'alam' ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'bg-white border border-slate-200 text-slate-600 hover:border-blue-400' ?>">
                    Alam
                </a>
            </div>
        </header>

        <!-- ════ GRID KARTU ════ -->
        <?php if (empty($listDestinasi)): ?>
        <div class="flex flex-col items-center justify-center py-32 text-center">
            <p class="text-6xl mb-4">🔍</p>
            <h3 class="text-xl font-black text-slate-700">Belum ada destinasi</h3>
            <p class="text-slate-400 mt-2 mb-6">Kategori <b><?= ucfirst($kategori) ?></b> belum memiliki konten.</p>
            <a href="tour.php?kategori=semua" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all">
                Lihat Semua Destinasi
            </a>
        </div>
        <?php else: ?>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($listDestinasi as $i => $dest): ?>
            <div class="bg-white rounded-[40px] overflow-hidden shadow-sm border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group cursor-pointer"
                 onclick="openModal(<?= $i ?>)">

                <!-- Foto + overlay play hint -->
                <div class="relative h-64 overflow-hidden">
                    <img src="<?= htmlspecialchars($dest['foto']) ?>"
                         alt="<?= htmlspecialchars($dest['nama']) ?>"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                         onerror="this.src='assets/candi.jpg'">

                    <!-- Overlay gelap + ikon play saat hover -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white/20 backdrop-blur-sm rounded-full p-4">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-black <?= $dest['badge_color'] ?> uppercase tracking-widest">
                        <?= htmlspecialchars($dest['badge']) ?>
                    </div>

                    <!-- Label "Tonton Video" -->
                    <?php if (!empty($dest['video_id'])): ?>
                    <div class="absolute bottom-4 right-4 bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        ▶ Tonton Video
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Konten -->
                <div class="p-8">
                    <h3 class="text-2xl font-black text-slate-800 mb-2"><?= htmlspecialchars($dest['nama']) ?></h3>
                    <p class="text-slate-500 text-sm mb-8 leading-relaxed line-clamp-2"><?= htmlspecialchars($dest['deskripsi']) ?></p>

                    <div class="bg-slate-50 rounded-[24px] p-5 flex items-center gap-4 border border-slate-100">
                        <button onclick="event.stopPropagation(); togglePlay(this)"
                                class="play-btn w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-orange-500 transition-all shadow-lg transform active:scale-90"
                                data-playing="false">
                            <span class="play-icon ml-1 text-lg">▶</span>
                            <span class="pause-icon text-lg hidden">⏸</span>
                        </button>
                        <div class="flex-1">
                            <div class="flex justify-between mb-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Audio Guide</span>
                                <span class="text-[10px] font-bold text-blue-600"><?= htmlspecialchars($dest['durasi']) ?></span>
                            </div>
                            <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="<?= $dest['progress'] ?> h-full bg-blue-600 rounded-full progress-fill"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- ════ MODAL VIDEO ════ -->
<div id="videoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop bg-black/60"
     onclick="closeModal(event)">

    <div id="modalBox"
         class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-3xl mx-4 overflow-hidden modal-enter">

        <!-- Tombol tutup -->
        <button onclick="closeModal()"
                class="absolute top-4 right-4 z-10 w-10 h-10 bg-slate-100 hover:bg-red-100 hover:text-red-600 rounded-full flex items-center justify-center text-slate-500 text-xl font-black transition-all">
            ✕
        </button>

        <!-- Video YouTube -->
        <div id="videoContainer" class="w-full bg-black aspect-video">
            <iframe id="youtubeFrame"
                    class="w-full h-full"
                    src=""
                    title="Video Wisata"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
            </iframe>
        </div>

        <!-- Info destinasi -->
        <div class="p-8">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h2 id="modalNama" class="text-2xl font-black text-slate-800"></h2>
                    <div class="flex gap-4 mt-2">
                        <span id="modalLokasi" class="text-xs text-slate-500 flex items-center gap-1">📍 </span>
                        <span id="modalJam" class="text-xs text-slate-500 flex items-center gap-1">🕐 </span>
                    </div>
                </div>
                <span id="modalBadge" class="text-xs font-black px-3 py-1 rounded-full bg-blue-50 text-blue-600 uppercase tracking-widest"></span>
            </div>
            <p id="modalDeskripsi" class="text-slate-600 text-sm leading-relaxed"></p>
        </div>
    </div>
</div>

<!-- ════ DATA JSON untuk JS ════ -->
<script>
const destinations = <?= json_encode(array_values($listDestinasi), JSON_UNESCAPED_UNICODE) ?>;

function openModal(index) {
    const d = destinations[index];
    const modal = document.getElementById('videoModal');
    const box   = document.getElementById('modalBox');

    // Isi konten
    document.getElementById('modalNama').textContent     = d.nama;
    document.getElementById('modalDeskripsi').textContent = d.deskripsi_panjang || d.deskripsi;
    document.getElementById('modalBadge').textContent    = d.badge;
    document.getElementById('modalLokasi').textContent   = '📍 ' + (d.lokasi || '-');
    document.getElementById('modalJam').textContent      = '🕐 ' + (d.jam_buka || '-');

    // Set video YouTube
    const frame = document.getElementById('youtubeFrame');
    if (d.video_id) {
        frame.src = `https://www.youtube.com/embed/${d.video_id}?autoplay=1&rel=0`;
        document.getElementById('videoContainer').classList.remove('hidden');
    } else {
        frame.src = '';
        document.getElementById('videoContainer').classList.add('hidden');
    }

    // Tampilkan modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    box.classList.remove('modal-exit');
    box.classList.add('modal-enter');
    document.body.style.overflow = 'hidden';
}

function closeModal(event) {
    // Jika klik di dalam box, jangan tutup
    if (event && document.getElementById('modalBox').contains(event.target)) return;

    const modal = document.getElementById('modalBox');
    modal.classList.remove('modal-enter');
    modal.classList.add('modal-exit');

    setTimeout(() => {
        document.getElementById('videoModal').classList.add('hidden');
        document.getElementById('videoModal').classList.remove('flex');
        document.getElementById('youtubeFrame').src = ''; // stop video
        document.body.style.overflow = '';
    }, 200);
}

// Tutup modal dengan tombol Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
});

// ════ Audio Guide Toggle ════
function togglePlay(btn) {
    const isPlaying  = btn.dataset.playing === 'true';
    const playIcon   = btn.querySelector('.play-icon');
    const pauseIcon  = btn.querySelector('.pause-icon');
    const progressEl = btn.closest('.bg-slate-50').querySelector('.progress-fill');

    document.querySelectorAll('.play-btn').forEach(b => {
        if (b !== btn) {
            b.dataset.playing = 'false';
            b.querySelector('.play-icon').classList.remove('hidden');
            b.querySelector('.pause-icon').classList.add('hidden');
            b.classList.remove('bg-orange-500');
            b.classList.add('bg-blue-600');
        }
    });

    if (!isPlaying) {
        btn.dataset.playing = 'true';
        playIcon.classList.add('hidden');
        pauseIcon.classList.remove('hidden');
        btn.classList.remove('bg-blue-600');
        btn.classList.add('bg-orange-500');
        if (progressEl) {
            progressEl.style.transition = 'width 60s linear';
            progressEl.style.width = '100%';
        }
    } else {
        btn.dataset.playing = 'false';
        playIcon.classList.remove('hidden');
        pauseIcon.classList.add('hidden');
        btn.classList.remove('bg-orange-500');
        btn.classList.add('bg-blue-600');
        if (progressEl) {
            const cur = progressEl.getBoundingClientRect().width;
            const par = progressEl.parentElement.getBoundingClientRect().width;
            progressEl.style.transition = 'none';
            progressEl.style.width = Math.round(cur / par * 100) + '%';
        }
    }
}
</script>
</body>
</html>