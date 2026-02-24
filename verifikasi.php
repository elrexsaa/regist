<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

// --- 1. KONEKSI DATABASE ---
$url_db = getenv('MYSQL_URL');
if ($url_db) {
    $db_parts = parse_url($url_db);
    $koneksi = mysqli_connect($db_parts['host'], $db_parts['user'], $db_parts['pass'], ltrim($db_parts['path'], '/'), $db_parts['port']);
} else {
    $koneksi = mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");
}

if (!isset($_SESSION['verif_email'])) { 
    header("Location: index.php"); 
    exit(); 
}

$email = $_SESSION['verif_email'];
$msg = "";
$is_locked = false;
$remaining_lock = 0;

// --- 2. CEK LIMITASI (RATE LIMITING) ---
$cek_limit = mysqli_query($koneksi, "SELECT login_attempts, last_attempt_time FROM pendaftar WHERE email='$email'");
$data_limit = mysqli_fetch_assoc($cek_limit);

if ($data_limit['login_attempts'] >= 3) {
    $last_time = strtotime($data_limit['last_attempt_time']);
    // Penalty: 2 menit kali jumlah kegagalan setelah 3x
    $penalty = ($data_limit['login_attempts'] - 2) * 120; 
    $wait_until = $last_time + $penalty;
    
    if (time() < $wait_until) {
        $is_locked = true;
        $remaining_lock = $wait_until - time();
    }
}

// --- 3. LOGIKA VERIFIKASI (AUTO-SUBMIT) ---
if (isset($_POST['otp']) && !$is_locked) {
    $kode = mysqli_real_escape_string($koneksi, $_POST['otp']);
    $res = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE email='$email' AND verification_code='$kode'");

    if (mysqli_num_rows($res) > 0) {
        // Sukses: Reset attempt dan set verified
        mysqli_query($koneksi, "UPDATE pendaftar SET is_verified=1, login_attempts=0 WHERE email='$email'");
        $user_data = mysqli_fetch_assoc($res);
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['user_nama'] = $user_data['nama'];
        $msg = "SUCCESS"; 
    } else {
        // Gagal: Tambah attempt
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
    <title>Security Verification | VIP Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #020617; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .otp-input:focus { border-color: #3b82f6; box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }
        .error-shake { animation: shake 0.2s ease-in-out 0s 2; border-color: rgba(239, 68, 68, 0.5); }
        .otp-box { transition: all 0.3s ease; }
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6 text-slate-300">

    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-600/10 blur-[120px] rounded-full"></div>
    </div>

    <div class="glass max-w-lg w-full rounded-[40px] p-10 relative overflow-hidden shadow-2xl">
        <div class="relative z-10 text-center">
            <div class="inline-flex p-5 rounded-3xl bg-blue-500/10 mb-6 border border-blue-500/20 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-extrabold text-white mb-3 tracking-tight">Verifikasi OTP</h2>
            <p class="text-slate-500 mb-10 leading-relaxed text-sm">Masukkan 6-digit kode keamanan yang telah kami kirimkan ke <br><span class="text-blue-400 font-bold"><?= $email ?></span></p>

            <?php if($is_locked): ?>
                <div class="bg-red-500/10 border border-red-500/20 p-5 rounded-3xl mb-8">
                    <p class="text-red-400 text-sm font-bold uppercase tracking-widest">Akses Dikunci Sementara</p>
                    <p class="text-red-400/70 text-xs mt-1">Silakan coba lagi dalam <span id="timer" class="font-black underline text-sm"><?= $remaining_lock ?></span> detik.</p>
                </div>
            <?php endif; ?>

            <form id="otpForm" method="POST" class="space-y-10">
                <div class="flex justify-between gap-2 md:gap-3" id="otp-container">
                    <?php for($i=1; $i<=6; $i++): ?>
                        <input type="number" maxlength="1" oninput="this.value=this.value.slice(0,1)"
                               class="otp-box w-12 h-16 md:w-16 md:h-20 glass rounded-2xl text-center text-3xl font-black text-white focus:outline-none otp-input border border-white/10 shadow-lg <?= $msg == 'FAILED' ? 'error-shake' : '' ?>"
                               inputmode="numeric" <?= $is_locked ? 'disabled' : '' ?> autocomplete="off">
                    <?php endfor; ?>
                </div>
                
                <input type="hidden" name="otp" id="final_otp">

                <div id="status_msg" class="h-6">
                    <?php 
                        if($msg == "SUCCESS") echo "<span class='text-green-400 font-bold animate-pulse'>Berhasil! Mengalihkan ke Dashboard...</span>";
                        if($msg == "FAILED") echo "<span class='text-red-400 font-bold'>Kode salah atau sudah kedaluwarsa!</span>";
                    ?>
                </div>

                <div class="pt-6 border-t border-white/5">
                    <p class="text-sm text-slate-500">
                        Belum menerima kode? 
                        <button type="button" id="resendBtn" class="text-blue-400 font-extrabold hover:text-blue-300 transition-colors ml-1 disabled:opacity-30 disabled:cursor-not-allowed">KIRIM ULANG</button>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-box');
        const finalInput = document.getElementById('final_otp');
        const form = document.getElementById('otpForm');

        // 1. Auto-Focus & Auto-Submit
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length > 0 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                // Cek jika sudah terisi 6 digit
                const otpValues = Array.from(inputs).map(i => i.value).join('');
                if (otpValues.length === 6) {
                    finalInput.value = otpValues;
                    form.submit();
                }
            });

            // Handle Backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Paste Logic
            input.addEventListener('paste', (e) => {
                const data = e.clipboardData.getData('text').slice(0, 6);
                if (data.length === 6) {
                    const chars = data.split('');
                    inputs.forEach((inp, i) => inp.value = chars[i]);
                    finalInput.value = data;
                    form.submit();
                }
            });
        });

        // 2. Lockout Timer Logic
        <?php if($is_locked): ?>
            let timeLeft = <?= $remaining_lock ?>;
            const timerDisplay = document.getElementById('timer');
            const countdown = setInterval(() => {
                timeLeft--;
                timerDisplay.innerText = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    window.location.reload();
                }
            }, 1000);
        <?php endif; ?>

        // 3. Success Redirect
        <?php if($msg == "SUCCESS"): ?>
            setTimeout(() => { window.location.href = 'dashboard.php'; }, 1500);
        <?php endif; ?>

        // 4. AJAX Resend OTP Logic
        document.getElementById('resendBtn').onclick = function() {
            const btn = this;
            const originalText = btn.innerText;
            
            btn.disabled = true;
            btn.innerText = "MENGIRIM...";

            fetch('kirim_ulang.php')
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        alert('Kode baru telah dikirim ke email lo!');
                        // Cooldown 60 detik
                        let cooldown = 60;
                        const timer = setInterval(() => {
                            cooldown--;
                            btn.innerText = `COOLDOWN (${cooldown}s)`;
                            if (cooldown <= 0) {
                                clearInterval(timer);
                                btn.disabled = false;
                                btn.innerText = originalText;
                            }
                        }, 1000);
                    } else {
                        alert('Gagal mengirim ulang. Cek koneksi server!');
                        btn.disabled = false;
                        btn.innerText = originalText;
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan fatal!');
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
        }
    </script>
</body>
</html>
