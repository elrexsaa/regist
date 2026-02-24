<?php
session_start();
// (Koneksi database sama seperti di atas)

if (isset($_POST['cek_otp'])) {
    $email = $_SESSION['verif_email'];
    $otp_input = $_POST['otp'];
    
    $res = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE email='$email' AND verification_code='$otp_input'");
    if (mysqli_num_rows($res) > 0) {
        mysqli_query($koneksi, "UPDATE pendaftar SET is_verified=1 WHERE email='$email'");
        echo "Akun Berhasil Diverifikasi! Silakan Login.";
        header("Location: index.php");
    } else {
        echo "Kode Salah!";
    }
}
?>
