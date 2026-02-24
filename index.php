<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Keren</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Form Pendaftaran 📝</h2>
        
        <?php
        // Cek apakah tombol daftar sudah diklik
        if (isset($_POST['daftar'])) {
            $nama = $_POST['nama'];
            $email = $_POST['email'];

            // Ssst! Di sini nanti kita masukkan kode untuk simpan ke database Railway
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>
                    Halo <b>$nama</b>, pendaftaranmu sedang diproses!
                  </div>";
        }
        ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" required 
                    class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email Aktif</label>
                <input type="email" name="email" required 
                    class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>

            <button type="submit" name="daftar" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors shadow-lg active:scale-95">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-gray-500 text-xs mt-6">
            &copy; 2026 Project Website Kamu
        </p>
    </div>

</body>
</html>
