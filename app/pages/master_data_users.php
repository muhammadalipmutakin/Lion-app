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
</style>

<div class="container mx-auto p-4 md:p-8">
    <h1 class="text-3xl md:text-4xl font-bold lp-red-text mb-6">
        <i class="fas fa-users mr-2"></i>Kelola Users
    </h1>

    <div x-data="usersApp()" x-init="fetchData()" class="bg-white p-4 md:p-6 rounded-lg shadow-md">
        <div class="mb-4 text-right">
            <button @click="openModal('add')" class="lp-orange-bg text-white py-2 px-4 rounded-md font-semibold hover:bg-orange-600 transition-colors duration-200">
                <i class="fas fa-user-plus mr-2"></i>Tambah User
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(user, index) in usersData" :key="user.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="index + 1"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.nama"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.username"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openModal('edit', user)" class="text-blue-600 hover:text-blue-900 mr-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="confirmDelete(user.id)" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="usersData.length === 0">
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data user.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isModalOpen" @click="isModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="saveUser" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modalTitle"></h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" x-model="form.nama" id="nama" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                            </div>
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" x-model="form.username" id="username" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                            </div>
                            <div x-show="mode === 'add'">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" x-model="form.password" id="password" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                            </div>
                            <div x-show="mode === 'edit'">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak ingin diubah)</label>
                                <input type="password" x-model="form.password" id="password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-4">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 lp-red-bg text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
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
        Alpine.data('usersApp', () => ({
            usersData: [],
            isModalOpen: false,
            modalTitle: '',
            mode: 'add', // 'add' or 'edit'
            form: {
                id: null,
                nama: '',
                username: '',
                password: ''
            },
            fetchData() {
                fetch('controller/master_data_users_controller.php?action=read')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.usersData = data.data;
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            },
            openModal(mode, data = null) {
                this.isModalOpen = true;
                this.mode = mode;
                if (mode === 'add') {
                    this.modalTitle = 'Tambah User';
                    this.form.id = null;
                    this.form.nama = '';
                    this.form.username = '';
                    this.form.password = '';
                } else if (mode === 'edit') {
                    this.modalTitle = 'Edit User';
                    this.form.id = data.id;
                    this.form.nama = data.nama;
                    this.form.username = data.username;
                    this.form.password = ''; // Kosongkan password saat edit
                }
            },
            saveUser() {
                const action = this.form.id ? 'update' : 'create';
                const formData = new FormData();
                formData.append('id', this.form.id);
                formData.append('nama', this.form.nama);
                formData.append('username', this.form.username);
                formData.append('password', this.form.password); // Kirim password (kosong atau terisi)
                formData.append('action', action);

                fetch('controller/master_data_users_controller.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Berhasil!', data.message, 'success');
                            this.fetchData();
                            this.isModalOpen = false;
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
            },
            confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d90f23',
                    cancelButtonColor: '#ff8b00',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.deleteUser(id);
                    }
                });
            },
            deleteUser(id) {
                fetch(`controller/master_data_users_controller.php?action=delete&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Dihapus!', data.message, 'success');
                            this.fetchData();
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
            }
        }));
    });
</script>