<?php
session_start();
include __DIR__ . '/config.php';

var_dump($_SESSION); // ← tambah ini
die();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Dashboard - DigiTour</title>
</head>
<body class="bg-[#F8FAFC]">
    <div class="flex flex-col md:flex-row min-h-screen">
        <nav class="w-full md:w-72 bg-white border-r border-slate-200 p-8 flex flex-col justify-between">
            <div>
                <div class="text-3xl font-black text-blue-600 mb-12 tracking-tighter italic">DigiTour<span class="text-orange-500">.</span></div>
                <div class="space-y-2">
                    <a href="/dashboard.php" class="flex items-center gap-4 p-4 bg-blue-600 text-white rounded-[24px] shadow-lg shadow-blue-100">
                        <span class="text-xl">🏠</span> <span class="font-bold">Dashboard</span>
                    </a>
                    <a href="/tour.php" class="flex items-center gap-4 p-4 text-slate-400 hover:bg-slate-50 hover:text-blue-600 rounded-[24px] transition-all">
                        <span class="text-xl">🎥</span> <span class="font-bold">Guide Library</span>
                    </a>
                    <a href="/map_real.php" class="flex items-center gap-4 p-4 text-slate-400 hover:bg-slate-50 hover:text-blue-600 rounded-[24px] transition-all">
                        <span class="text-xl">📍</span> <span class="font-bold">Peta Real-Time</span>
                    </a>
                </div>
            </div>
            <a href="/logout.php" class="flex items-center gap-4 p-4 text-red-400 hover:bg-red-50 rounded-[24px] transition-all">
                <span class="text-xl">🚪</span> <span class="font-bold">Keluar Akun</span>
            </a>
        </nav>

        <main class="flex-1 p-6 md:p-12 overflow-y-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                <div>
                    <h2 class="text-4xl font-black text-slate-800 tracking-tight">
                        Halo, <?php echo htmlspecialchars($userName); ?>! 👋
                    </h2>
                    <p class="text-slate-400 mt-1 font-medium">Siap untuk petualangan digital hari ini?</p>
                </div>
                <div class="flex items-center gap-4 bg-white p-2 pr-6 rounded-full shadow-sm border border-slate-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-xl">👤</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Status Akun</p>
                        <p class="text-sm font-black text-blue-600 uppercase"><?php echo htmlspecialchars($userRole); ?> Member</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-8 rounded-[35px] border border-slate-100 shadow-sm hover:translate-y-[-5px] transition-all">
                    <p class="text-4xl mb-4">🏆</p>
                    <h4 class="text-slate-400 font-bold text-sm uppercase">Total Point</h4>
                    <p class="text-3xl font-black text-slate-800">2,450</p>
                </div>
                <div class="bg-white p-8 rounded-[35px] border border-slate-100 shadow-sm hover:translate-y-[-5px] transition-all">
                    <p class="text-4xl mb-4">🏛️</p>
                    <h4 class="text-slate-400 font-bold text-sm uppercase">Destinasi Dikunjungi</h4>
                    <p class="text-3xl font-black text-slate-800">12</p>
                </div>
                <div class="bg-white p-8 rounded-[35px] border border-slate-100 shadow-sm hover:translate-y-[-5px] transition-all">
                    <p class="text-4xl mb-4">🎧</p>
                    <h4 class="text-slate-400 font-bold text-sm uppercase">Jam Mendengarkan</h4>
                    <p class="text-3xl font-black text-slate-800">45 Jam</p>
                </div>
            </div>

            <div class="relative bg-indigo-900 h-[400px] rounded-[50px] overflow-hidden shadow-2xl shadow-indigo-200 group">
                <img src="https://images.unsplash.com/photo-1596402184320-417d7178b2cd?auto=format&fit=crop&q=80&w=1200"
                     class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-indigo-900 via-transparent to-transparent"></div>
                <div class="absolute bottom-12 left-12 right-12">
                    <span class="bg-orange-500 text-white px-4 py-1 rounded-full text-xs font-bold tracking-widest uppercase">Rekomendasi</span>
                    <h3 class="text-5xl font-black text-white mt-4 mb-2">Misteri Relief Borobudur</h3>
                    <p class="text-indigo-100 max-w-xl text-lg opacity-80 mb-8">
                        Dengarkan narasi eksklusif tentang simbolisme tersembunyi di lantai ketiga candi terbesar di dunia.
                    </p>
                    <a href="/tour.php" class="inline-block bg-white text-indigo-900 px-10 py-5 rounded-[24px] font-black hover:bg-orange-500 hover:text-white transition-all transform hover:scale-105 active:scale-95 shadow-xl">
                        Lanjutkan Tur Sekarang 🚀
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>