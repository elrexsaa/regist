<?php
// --- KONEKSI (Sesuaikan dengan koneksi Railway lo) ---
$url_db = getenv('MYSQL_URL');
if ($url_db) {
    $db_parts = parse_url($url_db);
    $koneksi = mysqli_connect($db_parts['host'], $db_parts['user'], $db_parts['pass'], ltrim($db_parts['path'], '/'), $db_parts['port']);
} else {
    // Backup manual jika env tidak terbaca
    $koneksi = mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");
}

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// --- QUERY SQL UNTUK BUAT TABEL ---
$sql = "CREATE TABLE IF NOT EXISTS pendaftar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    katasandi VARCHAR(255) NOT NULL,
    verification_code VARCHAR(10),
    is_verified TINYINT(1) DEFAULT 0,
    login_attempts INT DEFAULT 0,
    last_attempt_time DATETIME NULL,
    foto_profil LONGTEXT DEFAULT NULL,
    level_member ENUM('Basic', 'Premium', 'VVIP') DEFAULT 'Basic',
    saldo DECIMAL(15,2) DEFAULT 0.00,
    nama_bank VARCHAR(50),
    nomor_rekening VARCHAR(50),
    atas_nama VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// --- EKSEKUSI ---
if (mysqli_query($koneksi, $sql)) {
    echo "<h1 style='color:green;'>MANTAP COK! Tabel 'pendaftar' Berhasil Dibuat.</h1>";
    echo "<p>Sekarang hapus file ini dari server demi keamanan.</p>";
} else {
    echo "<h1 style='color:red;'>GAGAL, ANJ:</h1> " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
?>
