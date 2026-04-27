<?php
$userId   = $_COOKIE['user_id']  ?? null;
$userName = $_COOKIE['username'] ?? null;
$userRole = $_COOKIE['userrole'] ?? null;

if (empty($userId) || $userRole !== 'admin') {
    header("Location: /login.php");
    exit;
}

include __DIR__ . '/config.php';

// --- CRUD DESTINASI ---
if (isset($_POST['tambah'])) {
    $nama         = mysqli_real_escape_string($conn, $_POST['nama_wisata']);
    $kat          = $_POST['kategori'];
    $provinsi_id  = $_POST['provinsi_id'];
    $kabupaten_id = $_POST['kabupaten_id'];
    $desc         = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $foto = '';
    if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === 0) {
        $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
        $ekstensi      = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $ukuran        = $_FILES['foto']['size'];

        if (!in_array($ekstensi, $ekstensiValid)) {
            die("Format foto tidak valid.");
        }
        if ($ukuran > 2 * 1024 * 1024) {
            die("Ukuran foto maksimal 2MB.");
        }

        // Konversi ke base64 dan simpan langsung ke DB
        $fileData  = file_get_contents($_FILES['foto']['tmp_name']);
        $base64    = base64_encode($fileData);
        $mimeType  = $_FILES['foto']['type'];
        $foto      = "data:$mimeType;base64,$base64";
    }

    $foto_escaped = mysqli_real_escape_string($conn, $foto);
    mysqli_query($conn, "INSERT INTO destinasi (nama_wisata, kategori, provinsi_id, kabupaten_id, deskripsi, foto) 
                         VALUES ('$nama','$kat','$provinsi_id','$kabupaten_id','$desc','$foto_escaped')");
    header("Location: manage_destinasi.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM destinasi WHERE id_destinasi=$id");
    header("Location: manage_destinasi.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM destinasi ORDER BY id_destinasi DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard Admin - DigiTour</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-item-active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .skeleton { animation: pulse 1.5s ease-in-out infinite; background:#e2e8f0; border-radius:8px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

<div class="flex flex-col md:flex-row min-h-screen">

    <!-- ════════════ SIDEBAR ════════════ -->
    <aside class="w-full md:w-80 bg-white border-r border-slate-200 p-8 flex flex-col z-20">
        <div class="flex items-center gap-3 mb-12">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                <i data-lucide="map" class="text-white w-6 h-6"></i>
            </div>
            <div class="text-2xl font-black text-slate-800 tracking-tighter">Digi<span class="text-blue-600">Tour.</span></div>
        </div>
        <nav class="space-y-3 flex-1">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4 ml-4">Admin Menu</p>
            <a href="index.php" class="flex items-center gap-4 px-5 py-4 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-2xl transition-all duration-300 group">
                <i data-lucide="home" class="w-5 h-5 group-hover:scale-110 transition"></i>
                <span class="font-bold text-sm">Beranda Utama</span>
            </a>
            <a href="manage_destinasi.php" class="sidebar-item-active flex items-center gap-4 px-5 py-4 text-white rounded-2xl transition-all duration-300 group">
                <i data-lucide="settings-2" class="w-5 h-5 group-hover:rotate-45 transition"></i>
                <span class="font-bold text-sm">Manage Wisata</span>
            </a>
        </nav>
        <div class="mt-auto pt-8 border-t border-slate-100">
            <a href="logout.php" class="flex items-center gap-4 px-5 py-4 text-red-500 hover:bg-red-50 rounded-2xl transition-all group">
                <i data-lucide="log-out" class="w-5 h-5 group-hover:-translate-x-1 transition"></i>
                <span class="font-bold text-sm">Keluar Sistem</span>
            </a>
        </div>
    </aside>

    <!-- ════════════ MAIN CONTENT ════════════ -->
    <main class="flex-1 p-6 md:p-14 lg:p-20 overflow-y-auto">

        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Panel Admin</h1>
                <p class="text-slate-500 mt-2">Selamat datang, <b>Administrator</b>! Data statistik terbaru telah siap.</p>
            </div>
            <div class="flex items-center gap-4 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                    <i data-lucide="database" class="w-6 h-6"></i>
                </div>
                <div class="pr-6">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Total Destinasi</p>
                    <p class="text-lg font-black text-slate-800"><?= mysqli_num_rows($result); ?></p>
                </div>
            </div>
        </header>

        <!-- ══════════ STATISTIK RINGKASAN ══════════ -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="stat-cards">
            <?php for($i=0;$i<4;$i++): ?>
            <div class="bg-white p-5 rounded-[24px] border border-slate-200/60 shadow-sm">
                <div class="skeleton h-3 w-20 mb-3"></div>
                <div class="skeleton h-7 w-28 mb-2"></div>
                <div class="skeleton h-3 w-16"></div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- ══════════ CHART SECTION ══════════ -->
        <div class="bg-white p-8 rounded-[35px] shadow-sm border border-slate-200/60 mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="font-bold text-slate-800 uppercase tracking-wider text-sm">
                        Trend Wisatawan Mancanegara
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1" id="chart-subtitle">Memuat data dari BPS...</p>
                </div>
                <div class="flex items-center gap-2">
                    <button id="btn-bulanan" onclick="gantiChart('bulanan')"
                        class="px-4 py-2 text-[11px] font-bold rounded-xl bg-blue-600 text-white transition-all">
                        Per Bulan
                    </button>
                    <button id="btn-kebangsaan" onclick="gantiChart('kebangsaan')"
                        class="px-4 py-2 text-[11px] font-bold rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition-all">
                        Top Negara
                    </button>
                    <button onclick="muat(true)" title="Refresh data"
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 hover:bg-blue-50 hover:text-blue-500 transition-all">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div id="chart-error" class="hidden mb-4 px-4 py-3 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-xs font-medium"></div>
            <div class="relative w-full" id="chart-wrap" style="height:300px;">
                <div id="chart-loading" class="absolute inset-0 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="text-xs text-slate-400 font-medium">Mengambil data BPS...</p>
                    </div>
                </div>
                <canvas id="bpsChart" style="display:none;"></canvas>
            </div>
            <p class="text-[10px] text-slate-300 mt-4 text-right">
                Sumber: Badan Pusat Statistik (BPS) · Variabel 1470 · Diambil via <code class="font-mono">api_wisman.php</code>
            </p>
        </div>

        <!-- ══════════ FORM + TABEL ══════════ -->
        <div class="grid lg:grid-cols-12 gap-10">

            <div class="lg:col-span-4 space-y-10">
                <!-- Form tambah destinasi -->
                <div class="bg-white p-8 rounded-[35px] shadow-xl shadow-slate-200/50 border border-white">
                    <h2 class="text-xl font-bold mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                        </span>
                        Entri Wisata Baru
                    </h2>
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="text" name="nama_wisata" placeholder="Nama Destinasi" required
                            class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <select name="kategori"
                            class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="Candi">🏛️ Candi &amp; Sejarah</option>
                            <option value="Alam">🌲 Wisata Alam</option>
                            <option value="Kuliner">🍲 Wisata Kuliner</option>
                            <option value="Religi">⛪ Wisata Religi</option>
                        </select>
                        <div class="grid grid-cols-2 gap-4">
                            <select id="select-provinsi" name="provinsi_id" required
                                class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-xs appearance-none outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Provinsi</option>
                            </select>
                            <select id="select-kabupaten" name="kabupaten_id" required disabled
                                class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-xs disabled:opacity-50 appearance-none outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Kabupaten</option>
                            </select>
                        </div>
                        <textarea name="deskripsi" placeholder="Deskripsi..." rows="3"
                            class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500"></textarea>

                        <!-- ── INPUT FOTO ── -->
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-2 block">Foto Destinasi</label>
                            <input type="file" name="foto" accept="image/*" id="inputFoto"
                                class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm text-slate-600
                                       file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                                       file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600
                                       hover:file:bg-blue-100 transition-all cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                            <!-- Preview foto sebelum upload -->
                            <div id="previewWrap" class="hidden mt-3">
                                <img id="previewImg" src="#" alt="Preview"
                                     class="w-full h-36 object-cover rounded-2xl border border-slate-100">
                            </div>
                        </div>

                        <button type="submit" name="tambah"
                            class="w-full py-5 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                            Simpan Data
                        </button>
                    </form>
                </div>

                <!-- Tabel top kebangsaan -->
                <div class="bg-white p-8 rounded-[35px] border border-slate-200/60" id="tabel-kebangsaan-wrap">
                    <h3 class="font-bold text-slate-800 italic uppercase tracking-wider text-[10px] mb-4">
                        Top 10 Negara Pengunjung
                    </h3>
                    <div class="space-y-3" id="tabel-kebangsaan">
                        <?php for($i=0;$i<5;$i++): ?>
                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                            <div class="skeleton h-3 w-24"></div>
                            <div class="skeleton h-3 w-14"></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Tabel Destinasi -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-[35px] shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="p-8 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800 italic uppercase tracking-wider text-sm">Daftar Destinasi Aktif</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Informasi Destinasi</th>
                                    <th class="p-6 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php
                                // Reset result pointer karena sudah dipakai untuk num_rows
                                mysqli_data_seek($result, 0);
                                while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="p-6">
                                        <div class="flex items-start gap-4">
                                            <!-- Tampilkan foto thumbnail jika ada -->
                                            <?php if (!empty($row['foto'])): ?>
<img src="<?= $row['foto'] ?>"
     alt="<?= htmlspecialchars($row['nama_wisata']) ?>"
     class="w-14 h-14 rounded-xl object-cover border border-slate-100 flex-shrink-0">
<?php else: ?>
<div class="w-14 h-14 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 flex-shrink-0">
    <i data-lucide="image" class="w-6 h-6"></i>
</div>
<?php endif; ?>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_wisata']); ?></span>
                                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded border border-blue-100"><?= htmlspecialchars($row['kategori']); ?></span>
                                                </div>
                                                <p class="text-[11px] text-slate-400 mt-1 line-clamp-1"><?= htmlspecialchars($row['deskripsi']); ?></p>
                                                <?php if (empty($row['foto'])): ?>
                                                <p class="text-[10px] text-orange-400 mt-1 flex items-center gap-1">
                                                    ⚠️ Belum ada foto
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="edit_destinasi.php?id=<?= $row['id_destinasi']; ?>"
                                               class="text-blue-400 hover:text-blue-600 transition-all" title="Edit">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </a>
                                            <a href="?hapus=<?= $row['id_destinasi']; ?>" onclick="return confirm('Hapus destinasi ini?')"
                                               class="text-red-400 hover:text-red-600 transition-all" title="Hapus">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ════════════ SCRIPTS ════════════ -->
<script>
lucide.createIcons();

// ── Preview foto sebelum upload ──
document.getElementById('inputFoto').addEventListener('change', function() {
    const file = this.files[0];
    const wrap = document.getElementById('previewWrap');
    const img  = document.getElementById('previewImg');
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            wrap.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        wrap.classList.add('hidden');
    }
});

// ─── State ───────────────────────────────────────────────
let chartInst   = null;
let wismanData  = null;
let modeSaat    = 'bulanan';

function fmt(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
}

function isiStatCards(r) {
    document.getElementById('stat-cards').innerHTML = `
        <div class="bg-white p-5 rounded-[24px] border border-slate-200/60 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Total Kunjungan</p>
            <p class="text-2xl font-black text-slate-800">${fmt(r.grand_total)}</p>
            <p class="text-[10px] text-slate-400 mt-1">Seluruh bulan</p>
        </div>
        <div class="bg-white p-5 rounded-[24px] border border-slate-200/60 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Bulan Tertinggi</p>
            <p class="text-2xl font-black text-blue-600">${fmt(r.nilai_tertinggi)}</p>
            <p class="text-[10px] text-slate-400 mt-1">${r.bulan_tertinggi}</p>
        </div>
        <div class="bg-white p-5 rounded-[24px] border border-slate-200/60 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Rata-rata / Bulan</p>
            <p class="text-2xl font-black text-slate-800">${fmt(r.rata_rata_bulan)}</p>
            <p class="text-[10px] text-slate-400 mt-1">Rerata tahunan</p>
        </div>
        <div class="bg-white p-5 rounded-[24px] border border-slate-200/60 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Sumber Data</p>
            <p class="text-lg font-black text-slate-800">BPS</p>
            <p class="text-[10px] text-slate-400 mt-1">Tahun ${wismanData.tahun}</p>
        </div>
    `;
}

function isiTabelKebangsaan(labels, values) {
    const maxVal = Math.max(...values) || 1;
    const html = labels.map((neg, i) => `
        <div class="p-3 bg-slate-50 rounded-xl">
            <div class="flex justify-between mb-1">
                <span class="text-[11px] font-bold text-slate-600">${neg}</span>
                <span class="text-[11px] font-black text-blue-600">${fmt(values[i])}</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-1.5">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width:${Math.round(values[i]/maxVal*100)}%"></div>
            </div>
        </div>
    `).join('');
    document.getElementById('tabel-kebangsaan').innerHTML = html;
}

function renderChart(mode) {
    if (!wismanData) return;
    modeSaat = mode;

    if (chartInst) { chartInst.destroy(); chartInst = null; }

    const canvas  = document.getElementById('bpsChart');
    const loading = document.getElementById('chart-loading');
    canvas.style.display = 'block';
    loading.style.display = 'none';

    const ctx = canvas.getContext('2d');

    if (mode === 'bulanan') {
        const labels = wismanData.per_bulan.labels;
        const values = wismanData.per_bulan.values;
        document.getElementById('chart-subtitle').textContent =
            `Jumlah kunjungan per bulan · Tahun ${wismanData.tahun}`;

        chartInst = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Kunjungan Wisman',
                    data: values,
                    backgroundColor: 'rgba(37,99,235,0.85)',
                    borderRadius: 10,
                    hoverBackgroundColor: '#1d4ed8',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${fmt(ctx.parsed.y)} kunjungan`
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 10, weight: 'bold' }, callback: v => fmt(v) }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, maxRotation: 30 }
                    }
                }
            }
        });

    } else {
        const labels = wismanData.per_kebangsaan.labels;
        const values = wismanData.per_kebangsaan.values;
        document.getElementById('chart-subtitle').textContent =
            `Top 10 negara asal pengunjung · Tahun ${wismanData.tahun}`;

        const wrapH = Math.max(300, labels.length * 38 + 60);
        document.getElementById('chart-wrap').style.height = wrapH + 'px';

        chartInst = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Kunjungan',
                    data: values,
                    backgroundColor: [
                        '#2563eb','#0f6e56','#d85a30','#ba7517',
                        '#993556','#3b6d11','#534ab7','#993c1d','#639922','#3c3489'
                    ],
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${fmt(ctx.parsed.x)} kunjungan`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 10, weight: 'bold' }, callback: v => fmt(v) }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: 'bold' } }
                    }
                }
            }
        });
    }
}

function gantiChart(mode) {
    document.getElementById('btn-bulanan').className =
        'px-4 py-2 text-[11px] font-bold rounded-xl transition-all ' +
        (mode === 'bulanan' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200');
    document.getElementById('btn-kebangsaan').className =
        'px-4 py-2 text-[11px] font-bold rounded-xl transition-all ' +
        (mode === 'kebangsaan' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200');

    document.getElementById('chart-wrap').style.height = '300px';
    renderChart(mode);
}

async function muat(forceRefresh = false) {
    const errBox  = document.getElementById('chart-error');
    const loading = document.getElementById('chart-loading');
    const canvas  = document.getElementById('bpsChart');

    errBox.classList.add('hidden');
    canvas.style.display = 'none';
    loading.style.display = 'flex';

    try {
        const url = 'api_wisman.php' + (forceRefresh ? '?t=' + Date.now() : '');
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();

        if (json.status !== 'success') throw new Error(json.message || 'Respons tidak valid');

        wismanData = json;

        isiStatCards(json.ringkasan);
        isiTabelKebangsaan(json.per_kebangsaan.labels, json.per_kebangsaan.values);
        renderChart(modeSaat);

    } catch (err) {
        loading.style.display = 'none';
        errBox.classList.remove('hidden');
        errBox.innerHTML = `
            <b>Gagal memuat data BPS:</b> ${err.message}
            &nbsp;&mdash;&nbsp;
            <button onclick="muat(true)" class="underline font-bold">Coba lagi</button>
        `;
        document.getElementById('stat-cards').innerHTML = `
            <div class="col-span-4 text-center text-xs text-slate-400 py-4">
                Data statistik tidak tersedia saat ini.
            </div>`;
        document.getElementById('tabel-kebangsaan').innerHTML =
            '<p class="text-xs text-slate-400 text-center py-4">Data tidak tersedia.</p>';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    muat();

    const selectProv = document.getElementById('select-provinsi');
    const selectKab  = document.getElementById('select-kabupaten');

    fetch('api_provinsi.php').then(r => r.json()).then(d => {
        selectProv.innerHTML = '<option value="">Provinsi</option>';
        d.data[1].forEach(p => {
            let o = document.createElement('option');
            o.value = p.domain_id;
            o.text  = p.domain_name;
            selectProv.appendChild(o);
        });
    }).catch(() => {});

    selectProv.addEventListener('change', function () {
        if (!this.value) { selectKab.disabled = true; return; }
        selectKab.disabled = true;
        selectKab.innerHTML = '<option>Memuat...</option>';
        fetch(`api_kabupaten.php?prov_id=${this.value}`).then(r => r.json()).then(d => {
            selectKab.innerHTML = '<option value="">Kabupaten</option>';
            selectKab.disabled  = false;
            d.data[1].forEach(k => {
                let o = document.createElement('option');
                o.value = k.domain_id;
                o.text  = k.domain_name;
                selectKab.appendChild(o);
            });
        }).catch(() => { selectKab.disabled = false; });
    });

    lucide.createIcons();
});
</script>
</body>
</html>