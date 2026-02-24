<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Mencegah error warning PHP ngerusak respon AJAX
error_reporting(0);
require 'vendor/autoload.php';
session_start();

// Hapus semua output buffer sebelumnya
ob_start();

// --- KONFIGURASI SMTP (PASTIKAN BENER!) ---
$my_gmail = "frankandrewwzz@gmail.com"; // Ganti email lo
$my_app_pass = "qxamtzotnzmfrxvi"; // Ganti app password lo

$url_db = getenv('MYSQL_URL');
if ($url_db) {
    $db_parts = parse_url($url_db);
    $koneksi = mysqli_connect($db_parts['host'], $db_parts['user'], $db_parts['pass'], ltrim($db_parts['path'], '/'), $db_parts['port']);
} else {
    $koneksi = mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");
}

if (isset($_SESSION['verif_email'])) {
    $email = $_SESSION['verif_email'];
    $otp_baru = rand(100000, 999999);

    // Update OTP di database
    $update = mysqli_query($koneksi, "UPDATE pendaftar SET verification_code = '$otp_baru' WHERE email = '$email'");

    if($update) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $my_gmail;
            $mail->Password   = $my_app_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($my_gmail, 'VIP Admin');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'KODE VERIFIKASI BARU';
            $mail->Body    = "Kode verifikasi baru Anda adalah: <b style='font-size:24px;'>$otp_baru</b>";

            $mail->send();
            
            // Bersihkan buffer dan kirim respon sukses
            ob_clean();
            echo "success";
        } catch (Exception $e) {
            ob_clean();
            echo "error_mail";
        }
    } else {
        ob_clean();
        echo "error_db";
    }
} else {
    ob_clean();
    echo "no_session";
}
exit();
