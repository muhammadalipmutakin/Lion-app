<?php
// Pastikan nama sesi sesuai dengan yang diatur di auth.php
session_name('lion_parcel_app');
session_start();

// Cek apakah sesi 'user_data' sudah ada
if (isset($_SESSION['user_data'])) {
    // Jika sudah ada, langsung alihkan ke halaman utama
    header("Location: app/");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lion Parcel - Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <link rel="icon" type="image/x-icon" href="./app/assets/icon.png">
</head>

<body class="bg-[#d90f23] flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl p-8 sm:p-10 animate-[fadeIn_1s_ease-in-out]">
        <div class="text-center mb-8">
            <img src="./app/assets/Lion.png" alt="Lion Parcel Logo" class="mx-auto h-20 mb-4">
            <h1 class="text-4xl font-extrabold text-[#d90f23] mb-2">Selamat Datang</h1>
            <p class="text-gray-500">Silakan masuk ke akun Anda untuk melanjutkan.</p>
        </div>

        <form id="loginForm" method="POST">
            <div id="errorMessage" class="mb-4 hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded" role="alert">
                <p class="font-bold text-sm">Login Gagal!</p>
                <p id="errorText" class="text-sm"></p>
            </div>

            <div class="mb-5">
                <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8b00] transition-colors duration-300" placeholder="Masukkan username Anda" required>
            </div>

            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#ff8b00] transition-colors duration-300" placeholder="Masukkan password Anda" required>
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-[#d90f23] transition-colors duration-300">
                        <i class="fa-solid" :class="{ 'fa-eye-slash': !showPassword, 'fa-eye': showPassword }"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#d90f23] text-white font-bold py-3 px-4 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-transform duration-300 ease-in-out transform hover:scale-[1.01]">
                Masuk
            </button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');

            // Sembunyikan pesan error sebelumnya
            errorMessage.classList.add('hidden');

            fetch('app/controller/auth.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Login berhasil, arahkan ke halaman utama
                        window.location.href = 'app/';
                    } else {
                        // Login gagal, tampilkan pesan error dan atur timeout
                        errorText.innerText = data.message;
                        errorMessage.classList.remove('hidden');

                        // Hilangkan alert setelah 5 detik
                        setTimeout(() => {
                            errorMessage.classList.add('hidden');
                        }, 5000); // 5000ms = 5 detik
                    }
                })
                .catch(error => {
                    // Tangani kesalahan jaringan atau server
                    console.error('Error:', error);
                    errorText.innerText = 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.';
                    errorMessage.classList.remove('hidden');

                    // Hilangkan alert setelah 5 detik
                    setTimeout(() => {
                        errorMessage.classList.add('hidden');
                    }, 100);
                });
        });
    </script>
</body>

</html>