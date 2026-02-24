<?php
// --- 1. HANDLING ERROR ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- 2. KONEKSI DATABASE ---
$url_db = getenv('MYSQL_URL');
if ($url_db) {
    $db_parts = parse_url($url_db);
    $koneksi = mysqli_connect($db_parts['host'], $db_parts['user'], $db_parts['pass'], ltrim($db_parts['path'], '/'), $db_parts['port']);
} else {
    $koneksi = @mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");
}

$notif_html = "";

// --- 3. LOGIKA SIMPAN DATA ---
if (isset($_POST['daftar'])) {
    if (!$koneksi) {
        $notif_html = "<div class='glass bg-red-500/20 border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-center'>Koneksi Database Gagal.</div>";
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

        $query = "INSERT INTO pendaftar (nama, email, username_tele, wa_nomor, wa_jenis, wa_umur, wa_status, wa_alasan, perangkat) 
                  VALUES ('$nama', '$email', '$tele', '$wa', '$wa_jenis', '$wa_umur', '$wa_status', '$wa_alasan', '$perangkat')";
        
        if (mysqli_query($koneksi, $query)) {
            $notif_html = "<div class='glass bg-green-500/20 border-green-500/50 text-green-400 p-4 rounded-2xl mb-8 text-center font-semibold animate-pulse'>Registrasi Berhasil Disimpan!</div>";
        } else {
            $notif_html = "<div class='glass bg-red-500/20 border-red-500/50 text-red-400 p-4 rounded-2xl mb-8 text-center'>Terjadi kesalahan sistem.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran VIP | Freelance WhatsApp 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; overflow-x: hidden; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-text { background: linear-gradient(90deg, #60a5fa, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .email-suggestion { cursor: pointer; transition: all 0.2s; background: #1e293b; }
        .email-suggestion:hover { background: #334155; color: #60a5fa; }
        .age-picker { height: 120px; overflow-y: scroll; scroll-snap-type: y mandatory; position: relative; }
        .age-picker div { scroll-snap-align: center; height: 40px; display: flex; align-items: center; justify-content: center; opacity: 0.3; transition: 0.3s; cursor: pointer; }
        .age-picker div.active { opacity: 1; font-weight: bold; color: #60a5fa; transform: scale(1.2); }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex items-center justify-center p-6">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/20 blur-[120px] rounded-full"></div>
    </div>

    <div class="glass max-w-2xl w-full rounded-[32px] p-8 md:p-12 shadow-2xl my-10">
        <div class="text-center mb-10">
            <span class="px-4 py-1.5 rounded-full bg-blue-500/10 text-blue-400 text-xs font-bold tracking-widest uppercase">Akses Anggota</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-4 mb-2 tracking-tight">Formulir <span class="gradient-text">Registrasi</span></h1>
            <p class="text-slate-400">Silakan lengkapi data valid di bawah ini.</p>
        </div>

        <?= $notif_html ?>

        <form action="" method="POST" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Identitas Lengkap</label>
                    <input type="text" name="nama" required placeholder="Contoh: Budi Santoso" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                </div>
                <div class="relative">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Alamat Email</label>
                    <input type="email" id="emailInput" name="email" autocomplete="off" required placeholder="budi@mail.com" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                    <div id="emailSuggestions" class="hidden absolute left-0 right-0 mt-2 glass rounded-xl overflow-hidden z-50 shadow-2xl"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Username Telegram</label>
                    <div class="relative">
                        <span class="absolute left-5 top-4 text-blue-500 font-bold">@</span>
                        <input type="text" name="username_tele" required placeholder="username_anda" class="w-full glass py-4 pl-10 pr-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Nomor WhatsApp</label>
                    <input type="number" name="wa_nomor" required placeholder="628123456789" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 ml-1 text-center">Umur Akun (Bulan)</label>
                    <input type="hidden" name="wa_umur_val" id="wa_umur_val" value="0">
                    <div class="age-picker glass rounded-2xl" id="agePicker">
                        </div>
                    <p class="text-[10px] text-center mt-2 text-slate-500 italic">*Scroll atau Klik pada angka</p>
                </div>
                <div class="flex flex-col justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Jenis Akun</label>
                        <select name="wa_jenis" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none bg-[#0f172a]">
                            <option value="WhatsApp Biasa">WhatsApp Standar</option>
                            <option value="WhatsApp Bisnis">WhatsApp Bisnis</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Kesehatan Akun</label>
                        <select name="wa_status" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 bg-[#0f172a]">
                            <option value="Lancar">Lancar / Bersih</option>
                            <option value="Sering Delay">Sering Delay</option>
                            <option value="Pernah Terblokir">Pernah Terblokir</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Catatan Tambahan (Opsional)</label>
                <textarea name="wa_alasan" rows="2" placeholder="Sebutkan alasan jika akun pernah terblokir..." class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 resize-none"></textarea>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 p-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Perangkat yang Digunakan:</span>
                <div class="flex gap-4">
                    <?php foreach(['Android', 'iOS', 'Lainnya'] as $dev): ?>
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <input type="radio" name="perangkat" value="<?= $dev ?>" <?= $dev=='Android'?'checked':'' ?> class="hidden peer">
                        <div class="w-5 h-5 border-2 border-slate-600 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all"></div>
                        <span class="text-sm font-semibold text-slate-400 peer-checked:text-blue-400"><?= $dev ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" name="daftar" class="w-full py-5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black uppercase tracking-widest shadow-[0_0_30px_rgba(37,99,235,0.3)] transition-all transform active:scale-[0.98]">
                Kirim Pendaftaran
            </button>
        </form>
    </div>

    <script>
        // --- EMAIL AUTO-COMPLETE LOGIC ---
        const emailInput = document.getElementById('emailInput');
        const suggestionBox = document.getElementById('emailSuggestions');
        const domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'icloud.com', 'hotmail.com'];

        emailInput.addEventListener('input', (e) => {
            const val = e.target.value;
            if (val.includes('@')) {
                const parts = val.split('@');
                const name = parts[0];
                const domainPart = parts[1].toLowerCase();
                
                // Cari domain yang sesuai dengan ketikan setelah @
                const filteredDomains = domains.filter(d => d.startsWith(domainPart));

                if (filteredDomains.length > 0) {
                    suggestionBox.innerHTML = filteredDomains.map(d => `
                        <div class="email-suggestion py-3 px-6 text-sm" onclick="selectEmail('${name}@${d}')">
                            ${name}<span class="text-blue-400 font-bold">@${d}</span>
                        </div>
                    `).join('');
                    suggestionBox.classList.remove('hidden');
                } else {
                    suggestionBox.classList.add('hidden');
                }
            } else {
                suggestionBox.classList.add('hidden');
            }
        });

        function selectEmail(val) {
            emailInput.value = val;
            suggestionBox.classList.add('hidden');
        }

        // --- AGE PICKER LOGIC ---
        const picker = document.getElementById('agePicker');
        const valInput = document.getElementById('wa_umur_val');
        
        for(let i=0; i<=60; i++) {
            const item = document.createElement('div');
            item.innerText = i + " Bln";
            item.dataset.val = i;
            // Tambahkan event click agar bisa dipilih tanpa scroll
            item.onclick = function() {
                picker.scrollTo({
                    top: i * 40,
                    behavior: 'smooth'
                });
                updateActive(i);
            };
            picker.appendChild(item);
        }

        function updateActive(val) {
            const items = picker.querySelectorAll('div');
            items.forEach(item => {
                item.classList.remove('active');
                if(item.dataset.val == val) {
                    item.classList.add('active');
                    valInput.value = val;
                }
            });
        }

        picker.addEventListener('scroll', () => {
            const index = Math.round(picker.scrollTop / 40);
            updateActive(index);
        });
        
        // Inisialisasi posisi awal
        setTimeout(() => { picker.scrollTop = 0; updateActive(0); }, 100);
    </script>
</body>
</html>
