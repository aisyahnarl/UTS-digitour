<?php include __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Login - DigiTour</title>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex items-center justify-center">

<div class="w-full max-w-md mx-auto p-6">
    <div class="text-center mb-10">
        <div class="text-4xl font-black text-blue-600 tracking-tighter italic">
            DigiTour<span class="text-orange-500">.</span>
        </div>
        <p class="text-slate-400 mt-2 font-medium">Masuk ke akun kamu</p>
    </div>

    <div class="bg-white rounded-[35px] shadow-sm border border-slate-100 p-10">

        <!-- Error banner -->
        <div id="error-box" class="hidden mb-6 px-4 py-3 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-sm font-medium text-center"></div>

        <div class="space-y-5">
            <div>
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Email</label>
                <input type="email" id="email" placeholder="email@kamu.com"
                    class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-slate-800">
            </div>
            <div>
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Password</label>
                <input type="password" id="password" placeholder="••••••••"
                    class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all text-slate-800">
            </div>

            <button id="btn-login"
                class="w-full py-4 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                <span id="btn-text">Masuk Sekarang</span>
                <svg id="btn-spinner" class="hidden animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </button>
        </div>

        <p class="text-center text-sm text-slate-400 mt-6">
            Belum punya akun?
            <a href="/register.php" class="text-blue-600 font-bold hover:underline">Daftar di sini</a>
        </p>
    </div>
</div>

<script>
$(function () {

    function doLogin() {
        const email    = $('#email').val().trim();
        const password = $('#password').val().trim();
        const $btn     = $('#btn-login');
        const $errBox  = $('#error-box');

        $errBox.addClass('hidden').text('');

        if (!email || !password) {
            $errBox.removeClass('hidden').text('Email dan password wajib diisi.');
            return;
        }

        // Loading state
        $btn.prop('disabled', true);
        $('#btn-text').text('Memproses...');
        $('#btn-spinner').removeClass('hidden');

        $.ajax({
            url     : '/auth.php',
            method  : 'POST',
            dataType: 'json',
            data    : { email, password },

            success: function (res) {
                if (res.status === 'success') {
                    $('#btn-text').text('Berhasil! Mengalihkan...');
                    // Langsung redirect tanpa reload manual
                    window.location.href = res.redirect;
                } else {
                    $errBox.removeClass('hidden').text(res.message || 'Login gagal.');
                    resetBtn();
                }
            },

            error: function (xhr) {
                let msg = 'Terjadi kesalahan server.';
                try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                $errBox.removeClass('hidden').text(msg);
                resetBtn();
            }
        });
    }

    function resetBtn() {
        $('#btn-login').prop('disabled', false);
        $('#btn-text').text('Masuk Sekarang');
        $('#btn-spinner').addClass('hidden');
    }

    // Klik tombol
    $('#btn-login').on('click', doLogin);

    // Enter di input
    $('#email, #password').on('keydown', function (e) {
        if (e.key === 'Enter') doLogin();
    });

});
</script>
</body>
</html>