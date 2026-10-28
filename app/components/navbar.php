<nav class="bg-white shadow fixed top-0 w-full z-20">
    <div class="container mx-auto md:px-4 flex md:justify-end justify-between  items-center">
        <div class="flex items-center">
            <a href="?page=dashboard" class="flex items-center space-x-2">
                <img src="./assets/Lion.png" alt="Lion Parcel Logo" class="w-60">
            </a>
        </div>
        <div class="flex items-center md:space-x-4" x-data="{ dropdownOpen: false }">
            <div class="text-gray-700">Selamat datang, <span class="font-bold"><?php echo htmlspecialchars($user_data['nama']); ?></span></div>
            <div class="relative">
                <button @click="dropdownOpen = !dropdownOpen" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-3 rounded inline-flex items-center transition-colors duration-200">
                    <i class="fas fa-user-circle mr-2"></i>
                    <i class="fas" :class="{'fa-chevron-up': dropdownOpen, 'fa-chevron-down': !dropdownOpen}"></i>
                </button>
                <ul x-show="dropdownOpen"
                    @click.outside="dropdownOpen = false"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-30 transform origin-top-right">
                    <li><a href="?page=profile" class="block px-4 py-2 text-gray-800 hover:bg-gray-100"><i class="fas fa-user mr-2"></i>Profil</a></li>
                    <li><a href="#" onclick="logout()" class="block px-4 py-2 text-red-600 hover:bg-red-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /**
     * Fungsi untuk menangani proses logout pengguna.
     * Menggunakan SweetAlert untuk konfirmasi dan fetch API untuk komunikasi dengan backend.
     */
    function logout() {
        Swal.fire({
            title: 'Yakin ingin keluar?',
            text: "Anda akan diarahkan kembali ke halaman login.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e31821', // Merah Lion Parcel
            cancelButtonColor: '#6b7280', // Abu-abu
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim permintaan POST ke logout.php
                fetch('controller/logout.php', {
                        method: 'POST'
                    })
                    .then(response => {
                        // Pastikan respons adalah JSON yang valid
                        if (!response.ok) {
                            throw new Error('Respons jaringan tidak OK');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            // Jika logout berhasil
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Arahkan ke halaman login setelah SweetAlert hilang
                                window.location.href = '../';
                            });
                        } else {
                            // Jika ada kesalahan dari server
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat logout.', 'error');
                        }
                    })
                    .catch(error => {
                        // Tangani kesalahan jaringan atau parsing JSON
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat mencoba logout. Silakan coba lagi.', 'error');
                    });
            }
        });
    }
</script>