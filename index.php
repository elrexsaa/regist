<?php
/**
 * VIP REGISTRATION SYSTEM 2026
 * Anti-Blank & Auto-Database Sync
 */

// 1. CEGAH HALAMAN MATI (Error Handling)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Kita pakai notif custom biar UI tetep rapi

// 2. CONFIG DATABASE (Railway Internal)
$host = "mysql.railway.internal"; 
$user = "root";
$pass = "BYNoqtolFWcLzImeCpMaisrFtEUhDJor";
$db   = "railway";
$port = "3306";

// Mencoba koneksi dengan peredam error (@)
$koneksi = @mysqli_connect($host, $user, $pass, $db, $port);

$notif_type = ""; 
$notif_msg = "";

if (!$koneksi) {
    $notif_type = "error";
    $notif_msg = "Database Offline: " . mysqli_connect_error();
} else {
    // 3. AUTO CREATE TABLE (Jika belum ada)
    $create_sql = "CREATE TABLE IF NOT EXISTS pendaftar (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100),
        email VARCHAR(100),
        username_tele VARCHAR(100),
        wa_nomor VARCHAR(20),
        wa_jenis VARCHAR(50),
        wa_umur INT,
        wa_status VARCHAR(50),
        wa_alasan TEXT,
        perangkat VARCHAR(50),
        waktu_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($koneksi, $create_sql);
}

// 4. PROSES SIMPAN DATA
if (isset($_POST['daftar'])) {
    if (!$koneksi) {
        $notif_type = "error";
        $notif_msg = "Gagal kirim: Database belum terkoneksi!";
    } else {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $email = mysqli_real_escape_string($koneksi, $_POST['email']);
        $tele = mysqli_real_escape_string($koneksi, $_POST['username_tele']);
        $wa = mysqli_real_escape_string($koneksi, $_POST['wa_nomor']);
        $wa_jenis = $_POST['wa_jenis'];
        $wa_umur = $_POST['wa_umur_val'];
        $wa_status = $_POST['wa_status'];
        $wa_alasan = mysqli_real_escape_string($koneksi, $_POST['wa_alasan']);
        $perangkat = $_POST['perangkat'];

        $sql = "INSERT INTO pendaftar (nama, email, username_tele, wa_nomor, wa_jenis, wa_umur, wa_status, wa_alasan, perangkat) 
                VALUES ('$nama', '$email', '$tele', '$wa', '$wa_jenis', '$wa_umur', '$wa_status', '$wa_alasan', '$perangkat')";
        
        if (mysqli_query($koneksi, $sql)) {
            $notif_type = "success";
            $notif_msg = "Access Granted! Data anda berhasil diamankan.";
        } else {
            $notif_type = "error";
            $notif_msg = "System Failure: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP Registration | Secure Line 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .gradient-text { background: linear-gradient(90deg, #60a5fa, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .input-style { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); padding: 1rem 1.5rem; border-radius: 1rem; outline: none; transition: 0.3s; }
        .input-style:focus { border-color: #60a5fa; box-shadow: 0 0 15px rgba(96,165,254,0.2); }
        .age-picker { height: 100px; overflow-y: scroll; scroll-snap-type: y mandatory; border-radius: 1rem; background: rgba(0,0,0,0.2); }
        .age-picker div { scroll-snap-align: center; height: 33px; display: flex; align-items: center; justify-content: center; opacity: 0.3; font-size: 0.8rem; }
        .age-picker div.active { opacity: 1; color: #60a5fa; font-weight: bold; transform: scale(1.2); }
        .suggestion-item { padding: 10px 20px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .suggestion-item:hover { background: rgba(96,165,254,0.1); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="fixed inset-0 -z-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-600/10 blur-[120px]"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-600/10 blur-[120px]"></div>
    </div>

    <div class="glass w-full max-w-xl rounded-[2.5rem] p-8 md:p-12 shadow-2xl relative">
        <div class="text-center mb-10">
            <span class="text-[10px] font-bold tracking-[0.3em] uppercase text-blue-400 bg-blue-400/10 px-3 py-1 rounded-full">Secure Connection</span>
            <h1 class="text-4xl font-extrabold mt-4 tracking-tighter">VIP <span class="gradient-text">Protocol</span></h1>
            <p class="text-slate-400 text-sm mt-2 font-light">Input identity for authorization system.</p>
        </div>

        <?php if ($notif_type == "success"): ?>
            <div class="bg-green-500/10 border border-green-500/50 text-green-400 p-4 rounded-2xl mb-8 text-center text-sm animate-pulse">
                <?= $notif_msg ?>
            </div>
        <?php elseif ($notif_type == "error"): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-center text-[10px] italic">
                <?= $notif_msg ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase ml-2 mb-2 block">Full Name</label>
                    <input type="text" name="nama" required placeholder="Ex: Alex Johnson" class="input-style">
                </div>
                <div class="relative">
                    <label class="text-[10px] font-bold text-slate-500 uppercase ml-2 mb-2 block">Email Verify</label>
                    <input type="email" id="emailInput" name="email" required placeholder="name@" class="input-style">
                    <div id="emailSuggestions" class="hidden absolute left-0 right-0 mt-2 glass rounded-xl z-50 overflow-hidden text-xs"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase ml-2 mb-2 block">Telegram ID</label>
                    <input type="text" name="username_tele" required placeholder="@username" class="input-style">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-500 uppercase ml-2 mb-2 block">WhatsApp Number</label>
                    <input type="number" name="wa_nomor" required placeholder="628..." class="input-style">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 items-center">
                <div class="text-center">
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">WA Age (Months)</label>
                    <input type="hidden" name="wa_umur_val" id="wa_umur_val" value="1">
                    <div class="age-picker" id="agePicker"></div>
                </div>
                <div class="space-y-3">
                    <select name="wa_jenis" class="input-style text-sm py-2 bg-[#0f172a]">
                        <option value="Standard">Standard Account</option>
                        <option value="Business">Business Account</option>
                    </select>
                    <select name="wa_status" class="input-style text-sm py-2 bg-[#0f172a]">
                        <option value="Optimized">Status: Optimized</option>
                        <option value="Latency">Status: Latency</option>
                        <option value="Flagged">Status: Flagged</option>
                    </select>
                </div>
            </div>

            <div>
                <textarea name="wa_alasan" placeholder="Additional Notes (Optional)" class="input-style h-20 resize-none text-sm"></textarea>
            </div>

            <div class="flex justify-between items-center px-2">
                <span class="text-[10px] font-bold text-slate-500 uppercase">Hardware:</span>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="radio" name="perangkat" value="Android" checked class="accent-blue-500"> Android
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="radio" name="perangkat" value="iOS" class="accent-blue-500"> iOS
                    </label>
                </div>
            </div>

            <button type="submit" name="daftar" class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:scale-[1.02] active:scale-95 transition-all text-white font-black tracking-widest text-sm shadow-xl shadow-blue-500/20">
                AUTHORIZE REGISTRATION
            </button>
        </form>
    </div>

    <script>
        // --- Email Suggestions ---
        const emailIn = document.getElementById('emailInput');
        const suggest = document.getElementById('emailSuggestions');
        const domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'admin.uk'];

        emailIn.addEventListener('input', () => {
            const v = emailIn.value;
            if(v.includes('@') && !v.split('@')[1].includes('.')) {
                const name = v.split('@')[0];
                suggest.innerHTML = domains.map(d => `<div class="suggestion-item" onclick="setE('${name}@${d}')">${name}<span class="text-blue-400">@${d}</span></div>`).join('');
                suggest.classList.remove('hidden');
            } else { suggest.classList.add('hidden'); }
        });
        function setE(v) { emailIn.value = v; suggest.classList.add('hidden'); }

        // --- Age Scroll Picker ---
        const picker = document.getElementById('agePicker');
        const inputU = document.getElementById('wa_umur_val');
        for(let i=0; i<=60; i++) {
            const d = document.createElement('div');
            d.innerText = i + " Mo"; d.dataset.v = i;
            picker.appendChild(d);
        }
        picker.addEventListener('scroll', () => {
            const items = picker.querySelectorAll('div');
            items.forEach(it => {
                const diff = Math.abs((it.offsetTop - picker.offsetTop) - picker.scrollTop - 33);
                it.classList.remove('active');
                if(diff < 15) { it.classList.add('active'); inputU.value = it.dataset.v; }
            });
        });
        picker.scrollTop = 1;
    </script>
</body>
</html>
