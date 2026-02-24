<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

// --- KONEKSI ---
$url_db = getenv('MYSQL_URL');
$koneksi = ($url_db) ? 
    mysqli_connect(parse_url($url_db)['host'], parse_url($url_db)['user'], parse_url($url_db)['pass'], ltrim(parse_url($url_db)['path'], '/'), parse_url($url_db)['port']) : 
    mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");

if (!isset($_SESSION['verif_email'])) { header("Location: index.php"); exit(); }

$email = $_SESSION['verif_email'];
$msg = "";
$is_locked = false;
$remaining_lock = 0;

// --- CEK LIMITASI ---
$cek_limit = mysqli_query($koneksi, "SELECT login_attempts, last_attempt_time FROM pendaftar WHERE email='$email'");
$data_limit = mysqli_fetch_assoc($cek_limit);

if ($data_limit['login_attempts'] >= 3) {
    $last_time = strtotime($data_limit['last_attempt_time']);
    $penalty = ($data_limit['login_attempts'] - 2) * 120; // Tambah 2 menit per kegagalan setelah 3x
    $wait_until = $last_time + $penalty;
    
    if (time() < $wait_until) {
        $is_locked = true;
        $remaining_lock = $wait_until - time();
    } else if ($data_limit['login_attempts'] >= 10) {
         // Reset jika sudah lewat waktu tapi attempts terlalu banyak (opsional)
    }
}

// --- LOGIKA VERIFIKASI ---
if (isset($_POST['otp']) && !$is_locked) {
    $kode = mysqli_real_escape_string($koneksi, $_POST['otp']);
    $res = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE email='$email' AND verification_code='$kode'");

    if (mysqli_num_rows($res) > 0) {
        mysqli_query($koneksi, "UPDATE pendaftar SET is_verified=1, login_attempts=0 WHERE email='$email'");
        $_SESSION['user_nama'] = mysqli_fetch_assoc($res)['nama'];
        $msg = "SUCCESS"; 
    } else {
        mysqli_query($koneksi, "UPDATE pendaftar SET login_attempts = login_attempts + 1, last_attempt_time = NOW() WHERE email='$email'");
        $msg = "FAILED";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #020617; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .otp-input:focus { border-color: #3b82f6; box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); transition: 0.3s; }
        .gradient-border { background: linear-gradient(to right, #3b82f6, #8b5cf6); }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        .error-shake { animation: shake 0.2s ease-in-out 0s 2; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6 text-slate-300">

    <div class="glass max-w-lg w-full rounded-[40px] p-10 relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-blue-600/20 blur-[80px] rounded-full"></div>
        
        <div class="relative z-10 text-center">
            <div class="inline-flex p-4 rounded-3xl bg-blue-500/10 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-extrabold text-white mb-2">Verifikasi Keamanan</h2>
            <p class="text-slate-500 mb-8 leading-relaxed">Kami telah mengirimkan kode 6-digit ke <br><span class="text-blue-400 font-semibold"><?= $email ?></span></p>

            <?php if($is_locked): ?>
                <div class="bg-red-500/10 border border-red-500/20 p-5 rounded-2xl mb-6">
                    <p class="text-red-400 text-sm font-bold">Terlalu banyak percobaan!</p>
                    <p class="text-red-400/70 text-xs mt-1">Silakan tunggu <span id="timer"><?= $remaining_lock ?></span> detik lagi.</p>
                </div>
            <?php endif; ?>

            <form id="otpForm" method="POST" class="space-y-8 <?= $msg == 'FAILED' ? 'error-shake' : '' ?>">
                <div class="flex justify-between gap-2 md:gap-4" id="otp-container">
                    <?php for($i=1; $i<=6; $i++): ?>
                        <input type="text" maxlength="1" 
                               class="otp-box w-12 h-16 md:w-14 md:h-20 glass rounded-2xl text-center text-2xl font-bold text-white focus:outline-none otp-input border border-white/10"
                               inputmode="numeric" <?= $is_locked ? 'disabled' : '' ?>>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="otp" id="final_otp">

                <div id="status_msg" class="text-sm font-bold">
                    <?php 
                        if($msg == "SUCCESS") echo "<span class='text-green-400'>Verified! Redirecting...</span>";
                        if($msg == "FAILED") echo "<span class='text-red-400'>Kode salah!</span>";
                    ?>
                </div>

                <div class="pt-4">
                    <p class="text-sm text-slate-500">Tidak menerima kode? 
                        <button type="button" id="resendBtn" class="text-blue-400 font-bold hover:underline disabled:opacity-50">Kirim Baru</button>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-box');
        const finalInput = document.getElementById('final_otp');
        const form = document.getElementById('otpForm');

        // Logic Auto-focus & Auto-submit
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length > 0 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                // Cek jika semua terisi
                const otp = Array.from(inputs).map(i => i.value).join('');
                if (otp.length === 6) {
                    finalInput.value = otp;
                    form.submit();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        // Timer Lockout
        <?php if($is_locked): ?>
            let timeLeft = <?= $remaining_lock ?>;
            const timerDisplay = document.getElementById('timer');
            const countdown = setInterval(() => {
                timeLeft--;
                timerDisplay.innerText = timeLeft;
                if (timeLeft <= 0) location.reload();
            }, 1000);
        <?php endif; ?>

        // Redirect on Success
        <?php if($msg == "SUCCESS"): ?>
            setTimeout(() => { window.location.href = 'dashboard.php'; }, 1500);
        <?php endif; ?>

        // Resend Logic (Simulasi)
        document.getElementById('resendBtn').onclick = function() {
            alert('Fitur Kirim Ulang sedang memproses email baru...');
            // Kamu bisa tambahkan AJAX kirim email disini
        }
    </script>
</body>
</html>
