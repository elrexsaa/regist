<?php
// --- CONFIG DATABASE (RAILWAY) ---
$host = "mysql.railway.internal"; 
$user = "root";
$pass = "BYNoqtolFWcLzImeCpMaisrFtEUhDJor";
$db   = "railway";
$port = "3306";

// Pakai @ supaya kalau koneksi gagal, halaman nggak blank putih
$koneksi = @mysqli_connect($host, $user, $pass, $db, $port);

$notif_pesan = "";
$notif_tipe = "";

// Cek apakah tombol daftar diklik
if (isset($_POST['daftar'])) {
    if (!$koneksi) {
        $notif_pesan = "Koneksi ke Database Gagal! Pastikan tabel sudah dibuat manual di Railway.";
        $notif_tipe = "error";
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
            $notif_pesan = "System: Data Authorized Successfully.";
            $notif_tipe = "success";
        } else {
            $notif_pesan = "Error Simpan: " . mysqli_error($koneksi);
            $notif_tipe = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP Registration | Freelance WhatsApp 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-text { background: linear-gradient(90deg, #60a5fa, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .email-suggestion { cursor: pointer; transition: all 0.2s; }
        .email-suggestion:hover { background: rgba(96, 165, 254, 0.2); }
        .age-picker { height: 120px; overflow-y: scroll; scroll-snap-type: y mandatory; background: rgba(0,0,0,0.2); border-radius: 1rem; }
        .age-picker div { scroll-snap-align: center; height: 40px; display: flex; align-items: center; justify-content: center; opacity: 0.3; transition: 0.3s; }
        .age-picker div.active { opacity: 1; font-weight: bold; color: #60a5fa; transform: scale(1.2); }
        input, textarea, select { background: rgba(255,255,255,0.05) !important; color: white !important; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/20 blur-[120px] rounded-full"></div>
    </div>

    <div class="glass max-w-2xl w-full rounded-[32px] p-8 md:p-12 shadow-2xl">
        <div class="text-center mb-10">
            <span class="px-4 py-1.5 rounded-full bg-blue-500/10 text-blue-400 text-xs font-bold tracking-widest uppercase">Member Access</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-4 mb-2 tracking-tight">Enter <span class="gradient-text">Registration</span></h1>
            <p class="text-slate-400">Silakan lengkapi data di bawah ini.</p>
        </div>

        <?php if ($notif_pesan != ""): ?>
            <div class="glass <?= $notif_tipe == 'success' ? 'bg-green-500/20 border-green-500/50 text-green-400' : 'bg-red-500/20 border-red-500/50 text-red-400' ?> p-4 rounded-2xl mb-8 text-center font-semibold">
                <?= $notif_pesan ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Full Identity</label>
                    <input type="text" name="nama" required placeholder="John Doe" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all placeholder:text-slate-600">
                </div>
                <div class="relative group">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                    <input type="email" id="emailInput" name="email" required placeholder="name" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all placeholder:text-slate-600">
                    <div id="emailSuggestions" class="hidden absolute left-0 right-0 mt-2 glass rounded-xl overflow-hidden z-50 shadow-2xl bg-slate-900"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Telegram Username</label>
                    <div class="relative">
                        <span class="absolute left-5 top-4 text-blue-500 font-bold">@</span>
                        <input type="text" name="username_tele" required placeholder="username" class="w-full glass py-4 pl-10 pr-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">WhatsApp Secure Line</label>
                    <input type="number" name="wa_nomor" required placeholder="628..." class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 ml-1 text-center">Account Age (Months)</label>
                    <input type="hidden" name="wa_umur_val" id="wa_umur_val" value="1">
                    <div class="age-picker" id="agePicker"></div>
                </div>
                <div class="flex flex-col justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Line Type</label>
                        <select name="wa_jenis" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 appearance-none">
                            <option class="bg-slate-900" value="WhatsApp Biasa">Standard Account</option>
                            <option class="bg-slate-900" value="WhatsApp Bisnis">Business Protocol</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Account Health</label>
                        <select name="wa_status" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                            <option class="bg-slate-900" value="Lancar">Optimized / Clean</option>
                            <option class="bg-slate-900" value="Sering Delay">Slight Latency</option>
                            <option class="bg-slate-900" value="Pernah Terblokir">Flagged Previously</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">Status Reason (Optional)</label>
                <textarea name="wa_alasan" rows="2" class="w-full glass py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 resize-none"></textarea>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 p-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Hardware Interface:</span>
                <div class="flex gap-4">
                    <?php foreach(['Android', 'iOS', 'Other'] as $dev): ?>
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <input type="radio" name="perangkat" value="<?= $dev ?>" <?= $dev=='Android'?'checked':'' ?> class="hidden peer">
                        <div class="w-5 h-5 border-2 border-slate-600 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all"></div>
                        <span class="text-sm font-semibold text-slate-400 peer-checked:text-blue-400"><?= $dev ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" name="daftar" class="w-full py-5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black uppercase tracking-widest shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_40px_rgba(37,99,235,0.5)] transition-all transform active:scale-[0.98]">
                Submit Application
            </button>
        </form>
    </div>

    <script>
        const emailInput = document.getElementById('emailInput');
        const suggestionBox = document.getElementById('emailSuggestions');
        const domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'icloud.com'];

        emailInput.addEventListener('input', (e) => {
            const val = e.target.value;
            if (val.includes('@') && !val.split('@')[1].includes('.')) {
                const name = val.split('@')[0];
                suggestionBox.innerHTML = domains.map(d => `<div class="email-suggestion py-3 px-6 text-sm" onclick="selectEmail('${name}@${d}')">${name}<span class="text-blue-400 font-bold">@${d}</span></div>`).join('');
                suggestionBox.classList.remove('hidden');
            } else {
                suggestionBox.classList.add('hidden');
            }
        });

        function selectEmail(val) {
            emailInput.value = val;
            suggestionBox.classList.add('hidden');
        }

        const picker = document.getElementById('agePicker');
        const valInput = document.getElementById('wa_umur_val');
        
        for(let i=0; i<=60; i++) {
            const item = document.createElement('div');
            item.innerText = i + " Mo";
            item.dataset.val = i;
            picker.appendChild(item);
        }

        picker.addEventListener('scroll', () => {
            const items = picker.querySelectorAll('div');
            let closest = null;
            let minDiff = Infinity;

            items.forEach(item => {
                const diff = Math.abs((item.offsetTop - picker.offsetTop) - picker.scrollTop - 40);
                item.classList.remove('active');
                if(diff < minDiff) {
                    minDiff = diff;
                    closest = item;
                }
            });

            if(closest) {
                closest.classList.add('active');
                valInput.value = closest.dataset.val;
            }
        });
        picker.scrollTop = 1; 
    </script>
</body>
</html>
