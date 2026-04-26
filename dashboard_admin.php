<?php
session_start();
include 'config.php';

// --- PROTEKSI KEAMANAN ---
// Jika tidak ada session role atau role-nya bukan admin, tendang ke login
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit;
}

// 1. PROSES CREATE
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_wisata']);
    $kat = $_POST['kategori'];
    $provinsi_id = $_POST['provinsi_id']; 
    $kabupaten_id = $_POST['kabupaten_id'];
    $desc = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    mysqli_query($conn, "INSERT INTO destinasi (nama_wisata, kategori, provinsi_id, kabupaten_id, deskripsi) VALUES ('$nama', '$kat', '$provinsi_id', '$kabupaten_id', '$desc')");
    header("Location: dashboard_admin.php"); // Arahkan kembali ke sini
    exit;
}

// 2. PROSES DELETE
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM destinasi WHERE id_destinasi=$id");
    header("Location: dashboard_admin.php");
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
    <title>Admin Dashboard - DigiTour</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-item-active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 custom-scrollbar">

    <div class="flex flex-col md:flex-row min-h-screen">
        <aside class="w-full md:w-80 bg-white border-r border-slate-200 p-8 flex flex-col z-20">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                    <i data-lucide="map" class="text-white w-6 h-6"></i>
                </div>
                <div class="text-2xl font-black text-slate-800 tracking-tighter">Digi<span class="text-blue-600">Tour.</span></div>
            </div>

            <nav class="space-y-3 flex-1">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4 ml-4">Admin Panel</p>
                
                <a href="dashboard_admin.php" class="sidebar-item-active flex items-center gap-4 px-5 py-4 text-white rounded-2xl transition-all duration-300 group">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 group-hover:scale-110 transition"></i>
                    <span class="font-bold text-sm">Dashboard Admin</span>
                </a>

                <a href="index.php" class="flex items-center gap-4 px-5 py-4 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-2xl transition-all duration-300 group">
                    <i data-lucide="eye" class="w-5 h-5 group-hover:scale-110 transition"></i>
                    <span class="font-bold text-sm">Lihat Web Utama</span>
                </a>
            </nav>

            <div class="mt-auto pt-8 border-t border-slate-100">
                <div class="flex items-center gap-3 px-5 py-4 mb-4 bg-slate-50 rounded-2xl">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-xs">
                        <?= substr($_SESSION['nama'], 0, 1); ?>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-slate-800 truncate"><?= $_SESSION['nama']; ?></p>
                        <p class="text-[10px] text-slate-400">Administrator</p>
                    </div>
                </div>
                <a href="logout.php" class="flex items-center gap-4 px-5 py-4 text-red-500 hover:bg-red-50 rounded-2xl transition-all group font-bold">
                    <i data-lucide="log-out" class="w-5 h-5 group-hover:-translate-x-1 transition"></i>
                    <span class="text-sm">Keluar Sistem</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-14 lg:p-20 overflow-y-auto">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Halo, <?= explode(' ', $_SESSION['nama'])[0]; ?>! 👋</h1>
                    <p class="text-slate-500 mt-2">Kelola seluruh data destinasi DigiTour di sini.</p>
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

            <div class="grid lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <div class="bg-white p-8 rounded-[35px] shadow-xl shadow-slate-200/50 border border-white sticky top-10">
                        <h2 class="text-xl font-bold mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                            </span>
                            Entri Wisata Baru
                        </h2>
                        
                        <form action="" method="POST" class="space-y-6">
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase mb-2 block ml-1">Nama Destinasi</label>
                                <input type="text" name="nama_wisata" placeholder="Misal: Candi Prambanan" required 
                                       class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase mb-2 block ml-1">Kategori Lokasi</label>
                                <select name="kategori" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all appearance-none">
                                    <option value="Candi">🏛️ Candi & Sejarah</option>
                                    <option value="Alam">🌲 Wisata Alam</option>
                                    <option value="Kuliner">🍲 Wisata Kuliner</option>
                                    <option value="Religi">⛪ Wisata Religi</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-slate-400 uppercase mb-2 block ml-1">Provinsi</label>
                                    <select id="select-provinsi" name="provinsi_id" required class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all appearance-none text-sm">
                                        <option value="">-- Memuat... --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-slate-400 uppercase mb-2 block ml-1">Kabupaten</label>
                                    <select id="select-kabupaten" name="kabupaten_id" required disabled class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all appearance-none text-sm disabled:opacity-50">
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase mb-2 block ml-1">Deskripsi Singkat</label>
                                <textarea name="deskripsi" placeholder="Ceritakan sedikit tentang tempat ini..." rows="4"
                                          class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"></textarea>
                            </div>

                            <button type="submit" name="tambah" 
                                    class="w-full py-5 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 transform active:scale-[0.98]">
                                Simpan Data
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="bg-white rounded-[35px] shadow-sm border border-slate-200/60 overflow-hidden">
                        <div class="p-8 border-b border-slate-100 flex justify-between items-center">
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
                                    <?php if(mysqli_num_rows($result) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="hover:bg-blue-50/30 transition-colors group">
                                            <td class="p-6">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0 text-slate-500 group-hover:bg-white transition">
                                                        <i data-lucide="image" class="w-5 h-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($row['nama_wisata']); ?></span>
                                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-md uppercase border border-blue-100">
                                                                <?= $row['kategori']; ?>
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-slate-500 mt-1 line-clamp-2 leading-relaxed italic">
                                                            <?= htmlspecialchars($row['deskripsi']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-6 text-center">
                                                <a href="?hapus=<?= $row['id_destinasi']; ?>" 
                                                   onclick="return confirm('Hapus data ini secara permanen?')"
                                                   class="inline-flex items-center justify-center w-10 h-10 bg-white text-red-500 rounded-xl border border-red-100 hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-200 transition-all duration-300">
                                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="p-20 text-center">
                                                <div class="flex flex-col items-center opacity-30">
                                                    <i data-lucide="folder-open" class="w-16 h-16 mb-4 text-slate-400"></i>
                                                    <p class="font-bold text-slate-500">Belum ada data destinasi</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
        document.addEventListener("DOMContentLoaded", function() {
            const selectProvinsi = document.getElementById('select-provinsi');
            const selectKabupaten = document.getElementById('select-kabupaten');

            fetch('api_provinsi.php')
                .then(response => response.json())
                .then(data => {
                    selectProvinsi.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                    const daftarProvinsi = data.data[1]; 
                    daftarProvinsi.forEach(provinsi => {
                        let option = document.createElement('option');
                        option.value = provinsi.domain_id;  
                        option.text = provinsi.domain_name; 
                        selectProvinsi.appendChild(option);
                    });
                });

            selectProvinsi.addEventListener('change', function() {
                const provId = this.value;
                if (!provId) {
                    selectKabupaten.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                    selectKabupaten.disabled = true;
                    return;
                }
                selectKabupaten.innerHTML = '<option value="">-- Memuat... --</option>';
                selectKabupaten.disabled = true;
                fetch(`api_kabupaten.php?prov_id=${provId}`)
                    .then(response => response.json())
                    .then(data => {
                        selectKabupaten.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
                        selectKabupaten.disabled = false;
                        const daftarKabupaten = data.data[1]; 
                        daftarKabupaten.forEach(kabupaten => {
                            let option = document.createElement('option');
                            option.value = kabupaten.domain_id;  
                            option.text = kabupaten.domain_name; 
                            selectKabupaten.appendChild(option);
                        });
                    });
            });
        });
    </script>
</body>
</html>