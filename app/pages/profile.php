<style>
    .lp-red-text {
        color: #d90f23;
    }

    .lp-red-bg {
        background-color: #d90f23;
    }

    .lp-orange-bg {
        background-color: #ff8b00;
    }

    .password-toggle-icon {
        cursor: pointer;
        position: absolute;
        right: 12px;
        /* Disesuaikan agar lebih ke kanan */
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .form-input-valid {
        border-color: #10b981;
        /* green-500 */
    }

    .form-input-invalid {
        border-color: #ef4444;
        /* red-500 */
    }
</style>


<div class="container mx-auto p-4 md:p-8">
    <h1 class="text-3xl md:text-4xl font-bold lp-red-text mb-6">
        <i class="fas fa-user-circle mr-2"></i>Profil Saya
    </h1>

    <div x-data="profileApp()" x-init="fetchUserData()" class="bg-white p-4 md:p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <div class="text-center mb-6">
            <div class="mx-auto bg-gray-200 rounded-full w-32 h-32 flex items-center justify-center mb-4">
                <i class="fas fa-user text-6xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800" x-text="userData.nama"></h2>
            <p class="text-gray-500" x-text="userData.username"></p>
        </div>

        <div class="text-center">
            <button @click="isModalOpen = true" class="lp-orange-bg text-white py-2 px-4 rounded-md font-semibold hover:bg-orange-600 transition-colors duration-200">
                <i class="fas fa-key mr-2"></i>Ubah Password
            </button>
        </div>

        <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isModalOpen" @click="isModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="changePassword" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Ubah Password</h3>
                        <div class="mt-4 space-y-4">
                            <div class="relative">
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                                <input :type="currentPasswordVisible ? 'text' : 'password'" x-model="form.current_password" id="current_password" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                                <span class="password-toggle-icon" @click="currentPasswordVisible = !currentPasswordVisible">
                                    <i class="fas" :class="{'fa-eye': !currentPasswordVisible, 'fa-eye-slash': currentPasswordVisible}"></i>
                                </span>
                            </div>
                            <div class="relative">
                                <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                <input :type="newPasswordVisible ? 'text' : 'password'" x-model="form.new_password" @input="validatePassword" id="new_password" required :class="{'form-input-valid': passwordValid, 'form-input-invalid': form.new_password !== '' && !passwordValid}" class="mt-1 block w-full p-2 border rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                                <span class="password-toggle-icon" @click="newPasswordVisible = !newPasswordVisible">
                                    <i class="fas" :class="{'fa-eye': !newPasswordVisible, 'fa-eye-slash': newPasswordVisible}"></i>
                                </span>
                            </div>
                            <div class="relative">
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                <input :type="confirmPasswordVisible ? 'text' : 'password'" x-model="form.confirm_password" @input="validatePassword" id="confirm_password" required :class="{'form-input-valid': passwordMatch, 'form-input-invalid': form.confirm_password !== '' && !passwordMatch}" class="mt-1 block w-full p-2 border rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                                <span class="password-toggle-icon" @click="confirmPasswordVisible = !confirmPasswordVisible">
                                    <i class="fas" :class="{'fa-eye': !confirmPasswordVisible, 'fa-eye-slash': confirmPasswordVisible}"></i>
                                </span>
                            </div>
                            <div x-show="form.new_password !== '' || form.confirm_password !== ''">
                                <p :class="{'text-green-600': passwordValid, 'text-red-600': !passwordValid}" class="text-sm">
                                    <span x-show="passwordValid"><i class="fas fa-check-circle mr-1"></i></span>
                                    <span x-show="!passwordValid"><i class="fas fa-times-circle mr-1"></i></span>
                                    Password baru minimal 8 karakter.
                                </p>
                                <p :class="{'text-green-600': passwordMatch, 'text-red-600': !passwordMatch}" class="text-sm">
                                    <span x-show="passwordMatch"><i class="fas fa-check-circle mr-1"></i></span>
                                    <span x-show="!passwordMatch"><i class="fas fa-times-circle mr-1"></i></span>
                                    Konfirmasi password cocok.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-4">
                            <button type="submit" :disabled="!passwordMatch || !passwordValid" :class="{'opacity-50 cursor-not-allowed': !passwordMatch || !passwordValid}" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 lp-red-bg text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Ubah Password
                            </button>
                            <button @click="isModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lp-orange-bg sm:mt-0 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profileApp', () => ({
            userData: {
                nama: 'Memuat...',
                username: 'Memuat...'
            },
            isModalOpen: false,
            form: {
                current_password: '',
                new_password: '',
                confirm_password: ''
            },
            currentPasswordVisible: false,
            newPasswordVisible: false,
            confirmPasswordVisible: false,
            passwordMatch: false,
            passwordValid: false,
            async fetchUserData() {
                try {
                    const response = await fetch('controller/user_controller.php?action=get_user_data');
                    const data = await response.json();
                    if (data.status === 'success') {
                        this.userData = data.data;
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error fetching user data:', error);
                    Swal.fire('Error', 'Gagal memuat data pengguna.', 'error');
                }
            },
            validatePassword() {
                // Validasi kecocokan password
                this.passwordMatch = this.form.new_password === this.form.confirm_password && this.form.new_password !== '';
                // Validasi panjang password
                this.passwordValid = this.form.new_password.length >= 8;
            },
            async changePassword() {
                if (!this.passwordMatch) {
                    Swal.fire('Gagal!', 'Konfirmasi password tidak cocok.', 'warning');
                    return;
                }
                if (!this.passwordValid) {
                    Swal.fire('Gagal!', 'Password baru minimal 8 karakter.', 'warning');
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'change_password');
                formData.append('current_password', this.form.current_password);
                formData.append('new_password', this.form.new_password);

                try {
                    const response = await fetch('controller/user_controller.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        Swal.fire('Berhasil!', data.message, 'success');
                        this.isModalOpen = false;
                        this.form.current_password = '';
                        this.form.new_password = '';
                        this.form.confirm_password = '';
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error changing password:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat mengubah password.', 'error');
                }
            }
        }));
    });
</script>