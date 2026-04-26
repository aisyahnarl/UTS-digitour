<?php 
session_start();  // ← ini yang kurang!
include __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <title>Daftar Akun - DigiTour</title>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-indigo-600 to-purple-800 min-h-screen flex items-center justify-center p-6">
    
    <a href="index.php" class="absolute top-8 left-8 text-white/70 hover:text-white flex items-center gap-2 transition font-bold text-sm">
        ← Kembali ke Beranda
    </a>

    <div class="bg-white w-full max-w-md rounded-[30px] shadow-2xl overflow-hidden">
        <div class="p-8 text-center">
            <h2 class="text-2xl font-black text-slate-800 mb-2">Buat Akun Baru</h2>
            <p class="text-slate-500 text-sm mb-8">Daftar sekarang untuk mulai menjelajahi destinasi.</p>

            <!-- Tampilkan pesan error/sukses -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 text-red-700 px-4 py-3 rounded-xl text-sm mb-4">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl text-sm mb-4">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="proses_register.php" method="POST" class="space-y-5 text-left">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="fullname" placeholder="Masukkan nama lengkap" required
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="nama@email.com" required
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" placeholder="Buat password aman" required
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95">
                    Buat Akun Baru
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-slate-500 text-sm">Sudah punya akun?
                    <a href="login.php" class="text-indigo-600 font-bold hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>