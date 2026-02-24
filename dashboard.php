<?php
session_start();
// Proteksi halaman: Jika belum login atau belum verifikasi, tendang ke login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VIP Dashboard | Freelance 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #020617; 
            color: #f8fafc;
            overflow-x: hidden;
        }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .nav-active { color: #3b82f6; transform: translateY(-5px); transition: all 0.3s ease; }
        
        /* Loading Bar Animation */
        #loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            width: 0%;
            z-index: 100;
            transition: width 0.4s ease;
        }

        /* Page Transition */
        .page-content {
            animation: slideUp 0.5s ease forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Saldo Card Gradient */
        .card-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #7e22ce 100%);
            box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="pb-24">

    <div id="loading-bar"></div>

    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden">
        <div class="absolute top-[-5%] right-[-5%] w-[60%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-5%] left-[-5%] w-[60%] h-[40%] bg-purple-600/10 blur-[120px] rounded-full"></div>
    </div>

    <header class="sticky top-0 z-40 w-full glass px-6 py-5 flex justify-between items-center rounded-b-[30px]">
        <div>
            <p class="text-[10px] font-bold text-blue-400 uppercase tracking-[3px]">VIP Member</p>
            <h1 id="page-title" class="text-xl font-extrabold tracking-tight">BERANDA</h1>
        </div>
        <div class="h-10 w-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-purple-600 flex items-center justify-center font-bold border border-white/20">
            <?= substr($_SESSION['user_nama'], 0, 1) ?>
        </div>
    </header>

    <main id="main-container" class="px-6 pt-8 space-y-6 overflow-y-auto">
        </main>

    <nav class="fixed bottom-6 left-6 right-6 h-20 glass rounded-[30px] flex items-center justify-around px-4 z-50 shadow-2xl">
        <button onclick="changePage('home')" class="nav-btn flex flex-col items-center gap-1 text-slate-500 transition-all" id="nav-home">
            <i data-lucide="home" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Home</span>
        </button>
        <button onclick="changePage('saldo')" class="nav-btn flex flex-col items-center gap-1 text-slate-500 transition-all" id="nav-saldo">
            <i data-lucide="wallet" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Saldo</span>
        </button>
        <button onclick="changePage('profile')" class="nav-btn flex flex-col items-center gap-1 text-slate-500 transition-all" id="nav-profile">
            <i data-lucide="user" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Profile</span>
        </button>
    </nav>

    <script>
        // Initialize Icons
        lucide.createIcons();

        const mainContainer = document.getElementById('main-container');
        const pageTitle = document.getElementById('page-title');
        const loadingBar = document.getElementById('loading-bar');

        // Page Templates
        const pages = {
            home: `
                <div class="page-content">
                    <div class="glass p-8 rounded-[35px] relative overflow-hidden">
                        <h2 class="text-2xl font-black mb-2">Halo, <?= $_SESSION['user_nama'] ?>! 👋</h2>
                        <p class="text-slate-400 text-sm leading-relaxed">Selamat datang kembali di portal eksklusif. Proyek baru sedang menunggumu hari ini.</p>
                        <div class="mt-6 flex gap-3">
                            <div class="px-4 py-2 bg-blue-600 rounded-xl text-xs font-bold shadow-lg shadow-blue-600/30">Cek Tugas</div>
                            <div class="px-4 py-2 glass rounded-xl text-xs font-bold border border-white/10">Bantuan</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="glass p-5 rounded-[30px]">
                            <p class="text-slate-500 text-[10px] font-bold uppercase">Proyek Selesai</p>
                            <p class="text-2xl font-black mt-1">12</p>
                        </div>
                        <div class="glass p-5 rounded-[30px]">
                            <p class="text-slate-500 text-[10px] font-bold uppercase">Rating VIP</p>
                            <p class="text-2xl font-black mt-1 text-yellow-400">4.9/5</p>
                        </div>
                    </div>
                </div>
            `,
            saldo: `
                <div class="page-content">
                    <div class="card-gradient p-8 rounded-[35px] text-white">
                        <div class="flex justify-between items-start mb-10">
                            <div>
                                <p class="text-[10px] font-bold opacity-70 uppercase tracking-widest">Saldo Saat Ini</p>
                                <h3 class="text-3xl font-black mt-1">Rp 4.500.000</h3>
                            </div>
                            <i data-lucide="layers" class="opacity-50"></i>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-xs font-medium">**** **** **** 2026</p>
                            <p class="text-xs font-bold uppercase">Platinum VIP</p>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold mt-8 mb-4">Riwayat Terakhir</h3>
                    <div class="space-y-4">
                        <div class="glass p-4 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-green-500/20 text-green-400 rounded-xl"><i data-lucide="arrow-down-left" class="w-5 h-5"></i></div>
                                <div><p class="text-sm font-bold">Project Web Design</p><p class="text-[10px] text-slate-500">24 Feb 2026</p></div>
                            </div>
                            <p class="text-sm font-black text-green-400">+Rp 1.2M</p>
                        </div>
                    </div>
                </div>
            `,
            profile: `
                <div class="page-content text-center">
                    <div class="relative inline-block mb-4">
                        <div class="h-24 w-24 rounded-[35px] bg-gradient-to-tr from-blue-600 to-purple-600 flex items-center justify-center text-4xl font-black border-4 border-white/5">
                            <?= substr($_SESSION['user_nama'], 0, 1) ?>
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-8 w-8 bg-blue-500 rounded-full border-4 border-[#020617] flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-black"><?= $_SESSION['user_nama'] ?></h2>
                    <p class="text-slate-500 text-sm mb-8"><?= $_SESSION['verif_email'] ?></p>
                    
                    <div class="space-y-3 text-left">
                        <div class="glass p-5 rounded-2xl flex items-center gap-4 border border-white/5">
                            <i data-lucide="settings" class="w-5 h-5 text-slate-400"></i>
                            <span class="text-sm font-bold">Pengaturan Akun</span>
                        </div>
                        <a href="logout.php" class="glass p-5 rounded-2xl flex items-center gap-4 border border-red-500/10 text-red-400">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                            <span class="text-sm font-bold">Keluar Aplikasi</span>
                        </a>
                    </div>
                </div>
            `
        };

        // Navigation Logic with Fake Loading
        function changePage(pageName) {
            // 1. Show Loading Bar
            loadingBar.style.width = '30%';
            
            // 2. Active Class Nav
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('nav-active'));
            document.getElementById(`nav-${pageName}`).classList.add('nav-active');

            setTimeout(() => {
                loadingBar.style.width = '70%';
                
                setTimeout(() => {
                    // 3. Render Content
                    mainContainer.innerHTML = pages[pageName];
                    pageTitle.innerText = pageName.toUpperCase();
                    lucide.createIcons();
                    
                    // 4. Complete Loading
                    loadingBar.style.width = '100%';
                    setTimeout(() => { loadingBar.style.width = '0%'; }, 300);
                }, 200);
            }, 300);
        }

        // Set Home as Default
        window.onload = () => changePage('home');
    </script>
</body>
</html>
