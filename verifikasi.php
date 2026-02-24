<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

// KONEKSI (Sama kayak index.php)
$url_db = getenv('MYSQL_URL');
if ($url_db) {
    $db_parts = parse_url($url_db);
    $koneksi = mysqli_connect($db_parts['host'], $db_parts['user'], $db_parts['pass'], ltrim($db_parts['path'], '/'), $db_parts['port']);
} else {
    $koneksi = mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");
}

$msg = "";
if (isset($_POST['verif'])) {
    $email = $_SESSION['verif_email'];
    $kode = mysqli_real_escape_string($koneksi, $_POST['otp']);
    
    $cek = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE email='$email' AND verification_code='$kode'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($koneksi, "UPDATE pendaftar SET is_verified=1 WHERE email='$email'");
        $msg = "<div class='bg-green-500/20 text-green-400 p-4 rounded-xl'>Email Berhasil Diverifikasi! Silakan Login.</div>";
    } else {
        $msg = "<div class='bg-red-500/20 text-red-400 p-4 rounded-xl'>Kode Salah atau Kadaluarsa!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background: #0f172a; font-family: sans-serif; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-6 text-slate-200">
    <div class="max-w-md w-full bg-white/5 backdrop-blur-lg border border-white/10 p-8 rounded-[32px] text-center">
        <h2 class="text-2xl font-bold mb-2">Cek Email Anda</h2>
        <p class="text-slate-400 mb-6">Masukkan 6 digit kode yang kami kirim ke <br><b><?= $_SESSION['verif_email'] ?></b></p>
        <?= $msg ?>
        <form action="" method="POST" class="mt-6 space-y-4">
            <input type="number" name="otp" placeholder="000000" required class="w-full bg-white/5 border border-white/10 py-4 px-6 rounded-2xl text-center text-2xl tracking-[1em] focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button name="verif" class="w-full py-4 bg-blue-600 rounded-2xl font-bold hover:bg-blue-500 transition-all">VERIFIKASI SEKARANG</button>
            <a href="index.php" class="block text-sm text-slate-500">Kembali ke Login</a>
        </form>
    </div>
</body>
</html>
