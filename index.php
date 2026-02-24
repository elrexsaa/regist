<?php
// 1. ERROR REPORTING (SUPAYA GAK BLANK)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. CONFIG DB
$host = "mysql.railway.internal"; 
$user = "root";
$pass = "BYNoqtolFWcLzImeCpMaisrFtEUhDJor";
$db   = "railway";
$port = "3306";

$notif = "";

// Cek koneksi dengan timeout singkat (biar gak nunggu kelamaan)
$koneksi = @mysqli_connect($host, $user, $pass, $db, $port);

if ($koneksi) {
    // Auto buat tabel
    $sql_tabel = "CREATE TABLE IF NOT EXISTS pendaftar (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100), email VARCHAR(100), username_tele VARCHAR(100),
        wa_nomor VARCHAR(20), wa_jenis VARCHAR(50), wa_umur INT,
        wa_status VARCHAR(50), wa_alasan TEXT, perangkat VARCHAR(50),
        waktu_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($koneksi, $sql_tabel);

    // Proses Simpan
    if (isset($_POST['daftar'])) {
        $nama = $_POST['nama']; $email = $_POST['email']; $tele = $_POST['username_tele'];
        $wa = $_POST['wa_nomor']; $wa_jenis = $_POST['wa_jenis']; $wa_umur = $_POST['wa_umur_val'];
        $wa_status = $_POST['wa_status']; $wa_alasan = $_POST['wa_alasan']; $perangkat = $_POST['perangkat'];

        $ins = "INSERT INTO pendaftar (nama, email, username_tele, wa_nomor, wa_jenis, wa_umur, wa_status, wa_alasan, perangkat) 
                VALUES ('$nama', '$email', '$tele', '$wa', '$wa_jenis', '$wa_umur', '$wa_status', '$wa_alasan', '$perangkat')";
        
        if (mysqli_query($koneksi, $ins)) {
            $notif = "OK";
        } else {
            $notif = "Gagal: " . mysqli_error($koneksi);
        }
    }
} else {
    // Jika koneksi gagal, notif aja, tapi jangan bikin mati halamannya
    if (isset($_POST['daftar'])) { $notif = "Database Offline, data belum tersimpan!"; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP Access Protocol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #020617; color: #e2e8f0; font-family: sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
        .age-picker { height: 90px; overflow-y: scroll; scroll-snap-type: y mandatory; background: rgba(0,0,0,0.3); border-radius: 12px; }
        .age-picker div { height: 30px; display: flex; align-items: center; justify-content: center; opacity: 0.3; scroll-snap-align: center; }
        .age-picker div.active { opacity: 1; color: #3b82f6; font-weight: bold; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="glass w-full max-w-lg rounded-[2rem] p-8 shadow-2xl border-t-blue-500/50">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black tracking-tighter text-white">SYSTEM <span class="text-blue-500">REGISTRY</span></h1>
            <p class="text-slate-500 text-sm">Authorized personnel only.</p>
        </div>

        <?php if($notif == "OK"): ?>
            <div class="bg-blue-600/20 border border-blue-500 text-blue-400 p-4 rounded-xl mb-6 text-center text-sm">Data Authorized Successfully!</div>
        <?php elseif($notif != ""): ?>
            <div class="bg-red-600/20 border border-red-500 text-red-400 p-4 rounded-xl mb-6 text-center text-xs italic"><?= $notif ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="nama" placeholder="Full Name" required class="w-full bg-slate-900/50 border border-slate-700 p-3 rounded-xl outline-none focus:border-blue-500">
                <input type="email" name="email" placeholder="Email" required class="w-full bg-slate-900/50 border border-slate-700 p-3 rounded-xl outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="username_tele" placeholder="Telegram ID" required class="w-full bg-slate-900/50 border border-slate-700 p-3 rounded-xl outline-none">
                <input type="number" name="wa_nomor" placeholder="WhatsApp (62...)" required class="w-full bg-slate-900/50 border border-slate-700 p-3 rounded-xl outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4 items-center">
                <div class="text-center">
                    <label class="text-[10px] text-slate-500 uppercase font-bold mb-1 block">Account Age (Mo)</label>
                    <input type="hidden" name="wa_umur_val" id="wa_umur_val" value="0">
                    <div class="age-picker" id="agePicker"></div>
                </div>
                <div class="space-y-2">
                    <select name="wa_jenis" class="w-full bg-slate-900 p-2 rounded-lg border border-slate-700 text-sm">
                        <option value="Biasa">Standard</option>
                        <option value="Bisnis">Business</option>
                    </select>
                    <select name="wa_status" class="w-full bg-slate-900 p-2 rounded-lg border border-slate-700 text-sm">
                        <option value="Lancar">Optimized</option>
                        <option value="Delay">Latency</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center text-xs px-2">
                <span>Hardware:</span>
                <label class="flex items-center gap-1"><input type="radio" name="perangkat" value="Android" checked> Android</label>
                <label class="flex items-center gap-1"><input type="radio" name="perangkat" value="iOS"> iOS</label>
            </div>

            <button type="submit" name="daftar" class="w-full bg-blue-600 hover:bg-blue-500 py-4 rounded-xl font-bold transition-all active:scale-95">SUBMIT PROTOCOL</button>
        </form>
    </div>

    <script>
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
                const diff = Math.abs((it.offsetTop - picker.offsetTop) - picker.scrollTop - 30);
                it.classList.remove('active');
                if(diff < 15) { it.classList.add('active'); inputU.value = it.dataset.v; }
            });
        });
        picker.scrollTop = 1;
    </script>
</body>
</html>
