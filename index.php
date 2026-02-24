<?php
session_start();
// Menghilangkan pesan warning yang mengganggu tampilan
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// --- 1. KONEKSI DATABASE ---
$url_db = getenv('MYSQL_URL');
if ($url_db) {
    $db_parts = parse_url($url_db);
    $koneksi = mysqli_connect($db_parts['host'], $db_parts['user'], $db_parts['pass'], ltrim($db_parts['path'], '/'), $db_parts['port']);
} else {
    $koneksi = @mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");
}

$notif_html = "";

// --- 2. LOGIKA LOGIN ---
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $pass  = $_POST['katasandi'];

    $res = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE email = '$email'");
    if ($res && mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        if (password_verify($pass, $user['katasandi'])) {
            $_SESSION['user'] = $user['nama'];
            $notif_html = "<div class='glass bg-green-500/20 border-green-500/50 text-green-400 p-4 rounded-2xl mb-8 text-center'>Login Berhasil! Selamat datang, ".$user['nama']."</div>";
        } else {
            $notif_html = "<div class='glass bg-red-500/20 border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-center'>Kata sandi salah!</div>";
        }
    } else {
        $notif_html = "<div class='glass bg-red-500/20 border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-center'>Email tidak terdaftar!</div>";
    }
}

// --- 3. LOGIKA DAFTAR (DI-FIX) ---
if (isset($_POST['daftar'])) {
    // Pastikan input password tidak kosong sebelum di-hash
    $raw_password = isset($_POST['katasandi']) ? $_POST['katasandi'] : '';
    
    if (empty($raw_password)) {
        $notif_html = "<div class='glass bg-red-500/20 border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-center'>Kata sandi wajib diisi!</div>";
    } else {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $email = mysqli_real_escape_string($koneksi, $_POST['email']);
        $sandi = password_hash($raw_password, PASSWORD_BCRYPT);
        $tele = mysqli_real_escape_string($koneksi, $_POST['username_tele']);
        $wa = mysqli_real_escape_string($koneksi, $_POST['wa_nomor']);
        $wa_jenis = $_POST['wa_jenis'];
        $wa_umur = $_POST['wa_umur_val'];
        $wa_status = $_POST['wa_status'];
        $wa_alasan = mysqli_real_escape_string($koneksi, $_POST['wa_alasan']);
        $perangkat = $_POST['perangkat'];

        // Cek email ganda
        $cek = mysqli_query($koneksi, "SELECT email FROM pendaftar WHERE email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $notif_html = "<div class='glass bg-yellow-500/20 border-yellow-500/50 text-yellow-400 p-4 rounded-2xl mb-8 text-center'>Email sudah digunakan!</div>";
        } else {
            $query = "INSERT INTO pendaftar (nama, email, katasandi, username_tele, wa_nomor, wa_jenis, wa_umur, wa_status, wa_alasan, perangkat) 
                      VALUES ('$nama', '$email', '$sandi', '$tele', '$wa', '$wa_jenis', '$wa_umur', '$wa_status', '$wa_alasan', '$perangkat')";
            
            if (mysqli_query($koneksi, $query)) {
                $notif_html = "<div class='glass bg-green-500/20 border-green-500/50 text-green-400 p-4 rounded-2xl mb-8 text-center'>Pendaftaran Berhasil! Silakan masuk menggunakan akun baru Anda.</div>";
            } else {
                $notif_html = "<div class='glass bg-red-500/20 border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-center'>Error Database: " . mysqli_error($koneksi) . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal VIP | Freelance 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-text { background: linear-gradient(90deg, #60a5fa, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        #emailSuggestions { max-height: 150px; overflow-y: auto; z-index: 100; }
        .email-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .email-item:hover { background: rgba(96, 165, 254, 0.2); }
        .age-picker { height: 120px; overflow-y: scroll; scroll-snap-type: y mandatory; }
        .age-picker div { scroll-snap-align: center; height: 40px; display: flex; align-items: center; justify-content: center; opacity: 0.3; transition: 0.3s; cursor: pointer; }
        .age-picker div.active { opacity: 1; font-weight: bold; color: #60a5fa; transform: scale(1.2); }
        .hidden-form { display: none; }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex items-center justify-center p-6">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/20 blur-[120px] rounded-full"></div>
    </div>

    <div class="glass max-w-2xl w-full rounded-[32px] p-8 md:p-12 shadow-2xl my-10">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold mb-2 tracking-tight" id="formTitle">MASUK <span class="gradient-text">VIP</span></h1>
            <p class="text-slate-400" id="formDesc">Gunakan email dan kata sandi Anda</p>
        </div>

        <?= $notif_html ?>

        <form action="" method="POST" id="loginForm" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Alamat Email</label>
                <input type="email" name="email" required placeholder="email@anda.com" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Kata Sandi</label>
                <input type="password" name="katasandi" required placeholder="••••••••" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            </div>
            <button type="submit" name="login" class="w-full py-5 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black shadow-lg transition-all transform active:scale-95">MASUK SEKARANG</button>
            <p class="text-center text-sm text-slate-400">Belum memiliki akun? <button type="button" onclick="showDaftar()" class="text-blue-400 font-bold underline">Daftar Akun Baru</button></p>
        </form>

        <form action="" method="POST" id="daftarForm" class="space-y-6 hidden-form">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Budi Santoso" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="relative">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Email</label>
                    <input type="email" id="emailInput" name="email" autocomplete="off" required placeholder="email@anda.com" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    <div id="emailSuggestions" class="hidden absolute left-0 right-0 mt-2 glass rounded-xl overflow-hidden z-50"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Buat Kata Sandi</label>
                    <input type="password" name="katasandi" required placeholder="••••••••" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Telegram Username</label>
                    <div class="relative">
                        <span class="absolute left-5 top-4 text-blue-500 font-bold">@</span>
                        <input type="text" name="username_tele" required placeholder="username" class="w-full glass py-4 pl-10 pr-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Nomor WhatsApp</label>
                    <input type="number" name="wa_nomor" required placeholder="628..." class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-4 text-center">Umur Akun WhatsApp</label>
                    <input type="hidden" name="wa_umur_val" id="wa_umur_val" value="0">
                    <div class="age-picker glass rounded-2xl" id="agePicker"></div>
                </div>
                <div class="space-y-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase ml-1">Pengaturan Akun</label>
                    <select name="wa_jenis" class="w-full glass py-4 px-6 rounded-2xl bg-[#0f172a]">
                        <option value="WhatsApp Biasa">WhatsApp Standar</option>
                        <option value="WhatsApp Bisnis">WhatsApp Bisnis</option>
                    </select>
                    <select name="wa_status" class="w-full glass py-4 px-6 rounded-2xl bg-[#0f172a]">
                        <option value="Lancar">Status: Lancar</option>
                        <option value="Pernah Terblokir">Status: Pernah Terblokir</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-between items-center p-4 glass rounded-2xl">
                <span class="text-xs font-bold text-slate-500 uppercase">Perangkat Utama:</span>
                <div class="flex gap-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="perangkat" value="Android" checked class="w-4 h-4 accent-blue-500">
                        <span class="text-sm">Android</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="perangkat" value="iOS" class="w-4 h-4 accent-blue-500">
                        <span class="text-sm">iOS</span>
                    </label>
                </div>
            </div>
            <button type="submit" name="daftar" class="w-full py-5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black shadow-lg transition-all transform active:scale-95">DAFTAR SEKARANG</button>
            <p class="text-center text-sm text-slate-400">Sudah punya akun? <button type="button" onclick="showLogin()" class="text-blue-400 font-bold underline">Kembali Login</button></p>
        </form>
    </div>

    <script>
        function showDaftar() {
            document.getElementById('loginForm').classList.add('hidden-form');
            document.getElementById('daftarForm').classList.remove('hidden-form');
            document.getElementById('formTitle').innerHTML = 'DAFTAR <span class="gradient-text">AKUN</span>';
            document.getElementById('formDesc').innerText = 'Lengkapi formulir pendaftaran di bawah';
        }
        function showLogin() {
            document.getElementById('daftarForm').classList.add('hidden-form');
            document.getElementById('loginForm').classList.remove('hidden-form');
            document.getElementById('formTitle').innerHTML = 'MASUK <span class="gradient-text">VIP</span>';
            document.getElementById('formDesc').innerText = 'Gunakan email dan kata sandi Anda';
        }

        const emailInput = document.getElementById('emailInput');
        const suggestionBox = document.getElementById('emailSuggestions');
        const domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com'];

        emailInput.addEventListener('input', (e) => {
            const val = e.target.value;
            if (val.includes('@')) {
                const parts = val.split('@');
                const name = parts[0];
                const domainPart = parts[1].toLowerCase();
                const filtered = domains.filter(d => d.startsWith(domainPart));
                if (filtered.length > 0) {
                    suggestionBox.innerHTML = filtered.map(d => `<div class="email-item" onclick="selectEmail('${name}@${d}')">${name}<span class="text-blue-400">@${d}</span></div>`).join('');
                    suggestionBox.classList.remove('hidden');
                } else { suggestionBox.classList.add('hidden'); }
            } else { suggestionBox.classList.add('hidden'); }
        });

        function selectEmail(val) {
            emailInput.value = val;
            suggestionBox.classList.add('hidden');
        }

        const picker = document.getElementById('agePicker');
        for(let i=0; i<=60; i++) {
            const item = document.createElement('div');
            item.innerText = i + " Bulan";
            item.dataset.val = i;
            item.onclick = () => { picker.scrollTo({ top: i * 40, behavior: 'smooth' }); };
            picker.appendChild(item);
        }
        picker.addEventListener('scroll', () => {
            const idx = Math.round(picker.scrollTop / 40);
            picker.querySelectorAll('div').forEach(el => el.classList.remove('active'));
            if(picker.children[idx]) {
                picker.children[idx].classList.add('active');
                document.getElementById('wa_umur_val').value = idx;
            }
        });
    </script>
</body>
</html>
