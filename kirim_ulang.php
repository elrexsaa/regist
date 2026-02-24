<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
session_start();

// --- KONFIGURASI SMTP (Samain sama index.php lo) ---
$my_gmail = "frankandrewwzz@gmail.com";
$my_app_pass = "qxamtzotnzmfrxvi";

$url_db = getenv('MYSQL_URL');
$koneksi = ($url_db) ? 
    mysqli_connect(parse_url($url_db)['host'], parse_url($url_db)['user'], parse_url($url_db)['pass'], ltrim(parse_url($url_db)['path'], '/'), parse_url($url_db)['port']) : 
    mysqli_connect("mysql.railway.internal", "root", "BYNoqtolFWcLzImeCpMaisrFtEUhDJor", "railway", "3306");

if (isset($_SESSION['verif_email'])) {
    $email = $_SESSION['verif_email'];
    $otp_baru = rand(100000, 999999);

    // Update OTP baru di database
    mysqli_query($koneksi, "UPDATE pendaftar SET verification_code = '$otp_baru' WHERE email = '$email'");

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
        $mail->Body    = "Kode verifikasi baru Anda adalah: <b style='font-size:20px;'>$otp_baru</b>";

        $mail->send();
        echo "success";
    } catch (Exception $e) {
        echo "error";
    }
}
?>
