<?php
$userId   = $_COOKIE['user_id']  ?? null;
$userName = $_COOKIE['username'] ?? null;
$userRole = $_COOKIE['userrole'] ?? null;

if (empty($userId) || $userRole !== 'admin') {
    header("Location: /login.php");
    exit;
}

include __DIR__ . '/config.php';

// Validasi ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: manage_destinasi.php");
    exit;
}

// Ambil data destinasi lama
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM destinasi WHERE id_destinasi=$id"));
if (!$row) {
    header("Location: manage_destinasi.php");
    exit;
}

$success = '';
$error   = '';

// ── Proses UPDATE ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama         = mysqli_real_escape_string($conn, $_POST['nama_wisata']);
    $kat          = $_POST['kategori'];
    $provinsi_id  = $_POST['provinsi_id'];
    $kabupaten_id = $_POST['kabupaten_id'];
    $desc         = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $foto_lama    = $row['foto'];
    $foto         = $foto_lama; // default: pakai foto lama

    // ── Hapus foto jika diminta ──
    if (isset($_POST['hapus_foto']) && !empty($foto_lama)) {
        if (file_exists($foto_lama)) unlink($foto_lama);
        $foto = '';
    }

    // ── Upload foto baru jika ada ──
    if (!empty($_FILES['foto']['name'])) {
        $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
        $ekstensi      = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $ukuran        = $_FILES['foto']['size'];

        if (!in_array($ekstensi, $ekstensiValid)) {
            $error = "Format foto tidak valid. Gunakan JPG, PNG, atau WEBP.";
        } elseif ($ukuran > 2 * 1024 * 1024) {
            $error = "Ukuran foto maksimal 2MB.";
        } else {
            // Hapus foto lama jika ada
            if (!empty($foto_lama) && file_exists($foto_lama)) {
                unlink($foto_lama);
            }

            if (!is_dir('assets')) mkdir('assets', 0755, true);

            $namaFile = 'assets/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['foto']['name']);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $namaFile)) {
                $foto = mysqli_real_escape_string($conn, $namaFile);
            } else {
                $error = "Gagal mengupload foto. Periksa permission folder assets/.";
            }
        }
    }

    if (empty($error)) {
        mysqli_query($conn,
            "UPDATE destinasi SET
                nama_wisata   = '$nama',
                kategori      = '$kat',
                provinsi_id   = '$provinsi_id',
                kabupaten_id  = '$kabupaten_id',
                deskripsi     = '$desc',
                foto          = '$foto'
             WHERE id_destinasi = $id"
        );
        // Reload data terbaru
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM destinasi WHERE id_destinasi=$id"));
        $success = "Data destinasi berhasil diperbarui!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <title>Edit Destinasi - DigiTour</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-item-active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
        }

        /* Drop zone styling */
        #drop-zone {
            transition: border-color 0.2s, background 0.2s;
        }
        #drop-zone.drag-over {
            border-color: #2563eb;
            background: #eff6ff;
        }
        #drop-zone.has-file {
            border-color: #22c55e;
            background: #f0fdf4;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

<div class="flex flex-col md:flex-row min-h-screen">

    <!-- ════════════ SIDEBAR ════════════ -->
    <aside class="w-full md:w-80 bg-white border-r border-slate-200 p-8 flex flex-col z-20 shrink-0">
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
        <header class="flex items-center gap-4 mb-10">
            <a href="manage_destinasi.php"
               class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Destinasi</h1>
                <p class="text-slate-400 text-sm mt-1">Perbarui informasi dan foto untuk <b class="text-slate-600"><?= htmlspecialchars($row['nama_wisata']) ?></b></p>
            </div>
        </header>

        <!-- Alert sukses -->
        <?php if ($success): ?>
        <div class="flex items-center gap-3 mb-8 px-6 py-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium">
            <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i>
            <?= htmlspecialchars($success) ?>
            <a href="manage_destinasi.php" class="ml-auto text-green-600 hover:text-green-800 font-bold underline">← Kembali ke daftar</a>
        </div>
        <?php endif; ?>

        <!-- Alert error -->
        <?php if ($error): ?>
        <div class="flex items-center gap-3 mb-8 px-6 py-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-sm font-medium">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Form Grid -->
        <form method="POST" enctype="multipart/form-data">
            <div class="grid lg:grid-cols-12 gap-8">

                <!-- ══ KOLOM KIRI: Info Destinasi ══════════════════════════ -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white p-8 rounded-[35px] shadow-sm border border-slate-200/60">
                        <h2 class="text-base font-bold text-slate-700 mb-6 flex items-center gap-2">
                            <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                <i data-lucide="info" class="w-4 h-4"></i>
                            </span>
                            Informasi Destinasi
                        </h2>

                        <div class="space-y-5">
                            <!-- Nama -->
                            <div>
                                <label class="text-xs font-bold text-slate-500 block mb-2">Nama Destinasi <span class="text-red-400">*</span></label>
                                <input type="text" name="nama_wisata" required
                                    value="<?= htmlspecialchars($row['nama_wisata']) ?>"
                                    class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm font-medium">
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label class="text-xs font-bold text-slate-500 block mb-2">Kategori</label>
                                <select name="kategori"
                                    class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                    <option value="Candi"   <?= $row['kategori']==='Candi'   ? 'selected':'' ?>>🏛️ Candi &amp; Sejarah</option>
                                    <option value="Alam"    <?= $row['kategori']==='Alam'    ? 'selected':'' ?>>🌲 Wisata Alam</option>
                                    <option value="Kuliner" <?= $row['kategori']==='Kuliner' ? 'selected':'' ?>>🍲 Wisata Kuliner</option>
                                    <option value="Religi"  <?= $row['kategori']==='Religi'  ? 'selected':'' ?>>⛪ Wisata Religi</option>
                                </select>
                            </div>

                            <!-- Provinsi & Kabupaten -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-slate-500 block mb-2">Provinsi <span class="text-red-400">*</span></label>
                                    <select id="select-provinsi" name="provinsi_id" required
                                        class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs appearance-none outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                        <option value="">Memuat...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-slate-500 block mb-2">Kabupaten <span class="text-red-400">*</span></label>
                                    <select id="select-kabupaten" name="kabupaten_id" required disabled
                                        class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs disabled:opacity-50 appearance-none outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                        <option value="">Kabupaten</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="text-xs font-bold text-slate-500 block mb-2">Deskripsi</label>
                                <textarea name="deskripsi" rows="5"
                                    class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm resize-none"
                                    placeholder="Tulis deskripsi destinasi..."><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══ KOLOM KANAN: Foto Destinasi ══════════════════════════ -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white p-8 rounded-[35px] shadow-sm border border-slate-200/60">
                        <h2 class="text-base font-bold text-slate-700 mb-6 flex items-center gap-2">
                            <span class="w-7 h-7 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                                <i data-lucide="image" class="w-4 h-4"></i>
                            </span>
                            Foto Destinasi
                        </h2>

                        <!-- Preview Foto Saat Ini -->
                        <div id="current-photo-wrap" class="<?= empty($row['foto']) || !file_exists($row['foto']) ? 'hidden' : '' ?> mb-5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Foto Saat Ini</p>
                            <div class="relative group rounded-2xl overflow-hidden border border-slate-200">
                                <img id="current-photo-img"
                                     src="<?= htmlspecialchars($row['foto'] ?? '') ?>"
                                     alt="Foto saat ini"
                                     class="w-full h-52 object-cover">
                                <!-- Overlay hapus -->
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <button type="button" onclick="konfirmasiHapusFoto()"
                                        class="bg-red-500 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 hover:bg-red-600 transition-all shadow-lg">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Foto
                                    </button>
                                </div>
                            </div>
                            <!-- Badge status -->
                            <div class="flex items-center gap-2 mt-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                <span class="text-[10px] text-slate-400 font-medium">Foto aktif tersimpan</span>
                                <button type="button" onclick="konfirmasiHapusFoto()"
                                    class="ml-auto text-[10px] text-red-400 hover:text-red-600 font-bold flex items-center gap-1 transition-all">
                                    <i data-lucide="x" class="w-3 h-3"></i> Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Input hapus foto (hidden) -->
                        <input type="checkbox" name="hapus_foto" id="cb-hapus-foto" class="hidden">

                        <!-- Status hapus foto -->
                        <div id="hapus-notice" class="hidden mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-2xl">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-400 shrink-0"></i>
                            <p class="text-xs text-red-600 font-medium">Foto akan dihapus saat menyimpan.</p>
                            <button type="button" onclick="batalHapusFoto()" class="ml-auto text-xs text-red-500 hover:text-red-700 font-bold underline">Batal</button>
                        </div>

                        <!-- Drop Zone Upload Foto Baru -->
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                                <?= (!empty($row['foto']) && file_exists($row['foto'])) ? 'Ganti Foto' : 'Upload Foto' ?>
                            </p>

                            <!-- Drop Area -->
                            <div id="drop-zone"
                                class="relative border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all"
                                onclick="document.getElementById('inputFoto').click()"
                                ondragover="dragOver(event)"
                                ondragleave="dragLeave(event)"
                                ondrop="dropFile(event)">

                                <!-- Ikon & teks default -->
                                <div id="dz-idle" class="">
                                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                        <i data-lucide="upload-cloud" class="w-6 h-6 text-slate-400"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-500">Klik atau seret foto ke sini</p>
                                    <p class="text-[11px] text-slate-400 mt-1">JPG, PNG, WEBP · Maks 2MB</p>
                                </div>

                                <!-- Preview foto baru (hidden) -->
                                <div id="dz-preview" class="hidden">
                                    <img id="preview-img" src="#" alt="Preview"
                                         class="w-full h-44 object-cover rounded-xl mb-3 border border-slate-200">
                                    <p id="preview-name" class="text-xs text-slate-500 font-medium truncate"></p>
                                    <button type="button" onclick="resetFoto(event)"
                                        class="mt-2 text-[11px] text-red-400 hover:text-red-600 font-bold flex items-center gap-1 mx-auto transition-all">
                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Batalkan pilihan
                                    </button>
                                </div>
                            </div>

                            <!-- Input file (tersembunyi) -->
                            <input type="file" id="inputFoto" name="foto" accept="image/jpeg,image/png,image/webp"
                                class="hidden" onchange="previewFoto(this.files[0])">

                            <p class="text-[10px] text-slate-300 mt-2">
                                Foto baru akan menggantikan foto lama secara otomatis.
                            </p>
                        </div>

                        <!-- Info saat ini tidak ada foto -->
                        <?php if (empty($row['foto']) || !file_exists($row['foto'])): ?>
                        <div class="mt-4 flex items-center gap-2 px-4 py-3 bg-orange-50 border border-orange-100 rounded-2xl">
                            <i data-lucide="image-off" class="w-4 h-4 text-orange-400 shrink-0"></i>
                            <p class="text-xs text-orange-500 font-medium">Destinasi ini belum memiliki foto.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="space-y-3">
                        <button type="submit"
                            class="w-full py-5 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 active:scale-[.98] transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2 text-sm">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Simpan Perubahan
                        </button>
                        <a href="manage_destinasi.php"
                            class="w-full py-4 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all flex items-center justify-center gap-2 text-sm">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Batal
                        </a>
                    </div>

                    <!-- Meta info -->
                    <div class="bg-white p-6 rounded-[24px] border border-slate-200/60 shadow-sm">
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Info Data</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">ID Destinasi</span>
                                <span class="text-xs font-bold text-slate-600">#<?= $id ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Kategori</span>
                                <span class="text-xs font-bold text-blue-600"><?= htmlspecialchars($row['kategori']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Status Foto</span>
                                <?php if (!empty($row['foto']) && file_exists($row['foto'])): ?>
                                <span class="text-xs font-bold text-green-600 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Ada
                                </span>
                                <?php else: ?>
                                <span class="text-xs font-bold text-orange-500 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-orange-400 rounded-full"></span> Belum Ada
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /grid -->
        </form>
    </main>
</div>

<!-- ════════════ SCRIPTS ════════════ -->
<script>
lucide.createIcons();

// ── Preview foto baru ──────────────────────────────────────────────────
function previewFoto(file) {
    if (!file) return;

    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('Ukuran file melebihi 2MB. Pilih file yang lebih kecil.');
        resetFoto();
        return;
    }

    const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        alert('Format file tidak valid. Gunakan JPG, PNG, atau WEBP.');
        resetFoto();
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-img').src  = e.target.result;
        document.getElementById('preview-name').textContent = file.name;
        document.getElementById('dz-idle').classList.add('hidden');
        document.getElementById('dz-preview').classList.remove('hidden');
        document.getElementById('drop-zone').classList.add('has-file');
        lucide.createIcons();
    };
    reader.readAsDataURL(file);
}

function resetFoto(event) {
    if (event) event.stopPropagation();
    document.getElementById('inputFoto').value        = '';
    document.getElementById('preview-img').src        = '#';
    document.getElementById('preview-name').textContent = '';
    document.getElementById('dz-preview').classList.add('hidden');
    document.getElementById('dz-idle').classList.remove('hidden');
    document.getElementById('drop-zone').classList.remove('has-file');
}

// ── Drag & Drop ────────────────────────────────────────────────────────
function dragOver(e) {
    e.preventDefault();
    document.getElementById('drop-zone').classList.add('drag-over');
}
function dragLeave(e) {
    document.getElementById('drop-zone').classList.remove('drag-over');
}
function dropFile(e) {
    e.preventDefault();
    document.getElementById('drop-zone').classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) {
        // Pasang ke input file agar ikut form
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('inputFoto').files = dt.files;
        previewFoto(file);
    }
}

// ── Hapus foto ─────────────────────────────────────────────────────────
function konfirmasiHapusFoto() {
    if (!confirm('Yakin ingin menghapus foto destinasi ini?')) return;
    document.getElementById('cb-hapus-foto').checked = true;
    document.getElementById('current-photo-wrap').classList.add('hidden');
    document.getElementById('hapus-notice').classList.remove('hidden');
    lucide.createIcons();
}

function batalHapusFoto() {
    document.getElementById('cb-hapus-foto').checked = false;
    document.getElementById('hapus-notice').classList.add('hidden');

    <?php if (!empty($row['foto']) && file_exists($row['foto'])): ?>
    document.getElementById('current-photo-wrap').classList.remove('hidden');
    <?php endif; ?>

    lucide.createIcons();
}

// ── Provinsi & Kabupaten ───────────────────────────────────────────────
const savedProvinsi  = "<?= $row['provinsi_id'] ?>";
const savedKabupaten = "<?= $row['kabupaten_id'] ?>";

document.addEventListener('DOMContentLoaded', function () {
    const selectProv = document.getElementById('select-provinsi');
    const selectKab  = document.getElementById('select-kabupaten');

    fetch('api_provinsi.php')
        .then(r => r.json())
        .then(d => {
            selectProv.innerHTML = '<option value="">Pilih Provinsi</option>';
            d.data[1].forEach(p => {
                const o = document.createElement('option');
                o.value = p.domain_id;
                o.text  = p.domain_name;
                if (p.domain_id == savedProvinsi) o.selected = true;
                selectProv.appendChild(o);
            });

            // Langsung load kabupaten jika ada provinsi tersimpan
            if (savedProvinsi) {
                muatKabupaten(savedProvinsi, savedKabupaten);
            }
        })
        .catch(() => {
            selectProv.innerHTML = '<option value="">Gagal memuat</option>';
        });

    selectProv.addEventListener('change', function () {
        if (!this.value) {
            selectKab.disabled = true;
            selectKab.innerHTML = '<option value="">Kabupaten</option>';
            return;
        }
        muatKabupaten(this.value, null);
    });
});

function muatKabupaten(provId, selectedKab) {
    const selectKab = document.getElementById('select-kabupaten');
    selectKab.disabled = true;
    selectKab.innerHTML = '<option>Memuat...</option>';

    fetch(`api_kabupaten.php?prov_id=${provId}`)
        .then(r => r.json())
        .then(d => {
            selectKab.innerHTML = '<option value="">Pilih Kabupaten</option>';
            d.data[1].forEach(k => {
                const o = document.createElement('option');
                o.value = k.domain_id;
                o.text  = k.domain_name;
                if (selectedKab && k.domain_id == selectedKab) o.selected = true;
                selectKab.appendChild(o);
            });
            selectKab.disabled = false;
        })
        .catch(() => {
            selectKab.innerHTML = '<option value="">Gagal memuat</option>';
            selectKab.disabled = false;
        });
}
</script>
</body>
</html>