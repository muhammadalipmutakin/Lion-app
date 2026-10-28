<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Movement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <style>
        /* Menggunakan warna khusus Anda */
        .lp-red-text {
            color: #d90f23;
        }

        .lp-red-bg {
            background-color: #d90f23;
        }

        .lp-orange-bg {
            background-color: #ff8b00;
        }

        /* Styling untuk Select2 agar full-width dan rapi */
        .select2-container .select2-selection--multiple {
            min-height: 42px !important;
            padding: 6px 12px;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #d90f23;
            color: white;
            border: 1px solid #d90f23;
            border-radius: 4px;
            padding: 2px 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            float: right;
            margin-left: 5px;
        }

        .select2-container {
            width: 100% !important;
        }

        .soft-green-bg {
            background-color: #d1f7e3;
        }

        .soft-red-bg {
            background-color: #fce7e7;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div id="print-area"></div>

    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl md:text-4xl font-bold lp-red-text mb-6">
            <i class="fas fa-truck-loading mr-2"></i>Histori Movement
        </h1>

        <div x-data="movementApp()" x-init="init()" class="bg-white p-4 md:p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                    <input type="date" id="start_date" x-model="startDate" @change="fetchMovementData()"
                        class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                    <input type="date" id="end_date" x-model="endDate" @change="fetchMovementData()"
                        class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg">
                </div>
                <div>
                    <label for="filter_item" class="block text-sm font-medium text-gray-700">Item</label>
                    <select id="filter_item" multiple class="w-full">
                        <option value=""></option>
                    </select>
                </div>
                <div>
                    <label for="filter_lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <select id="filter_lokasi" multiple class="w-full">
                        <option value=""></option>
                    </select>
                </div>
                <div>
                    <label for="filter_group" class="block text-sm font-medium text-gray-700">Grouping</label>
                    <select id="filter_group" multiple class="w-full">
                        <option value=""></option>
                    </select>
                </div>
                <div>
                    <label for="filter_type" class="block text-sm font-medium text-gray-700">Tipe</label>
                    <select id="filter_type" x-model="filterType" @change="fetchMovementData()"
                        class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Semua Tipe</option>
                        <option value="In">Masuk</option>
                        <option value="Out">Keluar</option>
                    </select>
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Aksi</label>
                    <div class="flex space-x-2">
                        <button @click="download('excel')"
                            class="w-full flex-1 p-2 bg-green-500 text-white rounded-md shadow-sm hover:bg-green-600 focus:ring-4 focus:ring-green-300 transition ease-in-out duration-150">
                            <i class="fas fa-file-excel mr-2"></i> Excel
                        </button>
                        <button @click="download('pdf')"
                            class="w-full flex-1 p-2 bg-red-500 text-white rounded-md shadow-sm hover:bg-red-600 focus:ring-4 focus:ring-red-300 transition ease-in-out duration-150">
                            <i class="fas fa-file-pdf mr-2"></i> PDF
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Group</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                STT/Resi</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                EAN</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IMEI/SN</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Qty</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal & Waktu</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(movement, index) in movementData" :key="movement.id">
                            <tr :class="{'soft-green-bg': movement.qty > 0, 'soft-red-bg': movement.qty < 0}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="index + 1"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="movement.item">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="movement.lokasi">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="movement.group">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="movement.stt_resi || '-'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="movement.ean || '-'">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="movement.imei || movement.sn || '-'"></td>
                                <td :class="{'text-green-600': movement.qty > 0, 'text-red-600': movement.qty < 0}"
                                    class="px-6 py-4 whitespace-nowrap text-sm font-bold" x-text="movement.qty"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="movement.created_at"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="movement.nama_user"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">
                                    <button @click="openEditModal(movement)"
                                        class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="deleteMovement(movement.id)"
                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button @click="printSingleMovement(movement)"
                                        class="text-gray-600 hover:text-gray-900" title="Print">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="movementData.length === 0">
                            <td colspan="11" class="px-6 py-4 text-center text-gray-500">Tidak ada data movement
                                ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div x-show="isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div x-show="isEditModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>

                <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center">
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="isEditModalOpen" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 align-middle max-w-lg w-full">
                        <div class="bg-lp-red-bg px-4 py-3 sm:px-6">
                            <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                                Edit Transaksi Movement
                            </h3>
                        </div>
                        <form @submit.prevent="saveMovement()">
                            <div class="p-4 sm:p-6 bg-white overflow-y-auto max-h-[70vh]">
                                <input type="hidden" x-model="editForm.id">

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="mb-4">
                                            <label for="edit_item"
                                                class="block text-sm font-medium text-gray-700">Item</label>
                                            <input type="text" id="edit_item" x-model="editForm.item"
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed"
                                                readonly>
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_lokasi"
                                                class="block text-sm font-medium text-gray-700">Lokasi</label>
                                            <select id="edit_lokasi" x-model="editForm.id_lokasi"
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                                                <template x-for="lokasi in allLocations" :key="lokasi.id">
                                                    <option :value="lokasi.id" x-text="lokasi.lokasi"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_group"
                                                class="block text-sm font-medium text-gray-700">Grouping</label>
                                            <select id="edit_group" x-model="editForm.id_group"
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                                                <template x-for="group in allGroups" :key="group.id">
                                                    <option :value="group.id" x-text="group.group"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_qty"
                                                class="block text-sm font-medium text-gray-700">Qty</label>
                                            <input type="number" id="edit_qty" x-model="editForm.qty"
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="mb-4">
                                            <label for="edit_stt_resi"
                                                class="block text-sm font-medium text-gray-700">STT/Resi</label>
                                            <input type="text" id="edit_stt_resi" x-model="editForm.stt_resi"
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_ean"
                                                class="block text-sm font-medium text-gray-700">EAN</label>
                                            <input type="text" id="edit_ean" x-model="editForm.ean"
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                                        </div>
                                        <div class="mb-4">
                                            <label for="edit_imei"
                                                class="block text-sm font-medium text-gray-700">IMEI/SN</label>
                                            <input type="text" id="edit_imei" x-model="editForm.imei"
                                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Simpan
                                </button>
                                <button @click="isEditModalOpen = false" type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-red-500 text-base font-medium text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lp-orange-bg sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
            Alpine.data('movementApp', () => ({
                movementData: [],
                allGroups: [],
                allLocations: [],
                allItems: [],

                startDate: '',
                endDate: '',
                filterItems: [],
                filterLokasis: [],
                filterGroups: [],
                filterType: '',

                isEditModalOpen: false,
                editForm: {
                    id: '',
                    item: '',
                    id_lokasi: '',
                    id_group: '',
                    qty: '',
                    stt_resi: '',
                    ean: '',
                    imei: ''
                },

                init() {
                    const today = this.getLocalDateString();
                    this.startDate = today;
                    this.endDate = today;

                    this.fetchDropdownData().then(() => {
                        this.setupSelect2();
                        this.fetchMovementData();
                    });
                },

                getLocalDateString() {
                    const date = new Date();
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                setupSelect2() {
                    this.populateSelect2('filter_item', this.allItems, 'item');
                    this.populateSelect2('filter_lokasi', this.allLocations, 'lokasi');
                    this.populateSelect2('filter_group', this.allGroups, 'group');

                    $('#filter_item').on('change', (e) => {
                        this.filterItems = $(e.target).val() || [];
                        this.fetchMovementData();
                    });
                    $('#filter_lokasi').on('change', (e) => {
                        this.filterLokasis = $(e.target).val() || [];
                        this.fetchMovementData();
                    });
                    $('#filter_group').on('change', (e) => {
                        this.filterGroups = $(e.target).val() || [];
                        this.fetchMovementData();
                    });
                },

                populateSelect2(id, data, textKey) {
                    const placeholder = `Semua ${textKey.charAt(0).toUpperCase() + textKey.slice(1)}`;
                    const options = data.map(item => ({
                        id: item.id || item[textKey],
                        text: item[textKey]
                    }));

                    $(`#${id}`).empty().select2({
                        data: options,
                        placeholder: placeholder,
                        allowClear: true,
                        closeOnSelect: false
                    });
                },

                async fetchDropdownData() {
                    try {
                        const response = await fetch('controller/movement_controller.php?action=get_dropdown_data');
                        const data = await response.json();

                        if (data.status === 'success') {
                            this.allGroups = data.group;
                            this.allLocations = data.lokasi;
                            this.allItems = data.item;
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error fetching dropdown data:', error);
                    }
                },

                async fetchMovementData() {
                    const params = new URLSearchParams();
                    params.append('start_date', this.startDate);
                    params.append('end_date', this.endDate);
                    params.append('type', this.filterType);

                    this.filterItems.forEach(item => params.append('items[]', item));
                    this.filterLokasis.forEach(lokasi => params.append('lokasis[]', lokasi));
                    this.filterGroups.forEach(group => params.append('groups[]', group));

                    const url = `controller/movement_controller.php?action=get_movement_data&${params.toString()}`;

                    try {
                        const response = await fetch(url);
                        const data = await response.json();

                        if (data.status === 'success') {
                            this.movementData = data.movements;
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error fetching movement data:', error);
                    }
                },

                openEditModal(movement) {
                    this.editForm = {
                        ...movement
                    };
                    this.isEditModalOpen = true;
                },

                async saveMovement() {
                    try {
                        const response = await fetch('controller/movement_controller.php?action=update_movement', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.editForm)
                        });
                        const data = await response.json();

                        if (data.status === 'success') {
                            Swal.fire('Berhasil!', 'Data transaksi berhasil diperbarui.', 'success');
                            this.isEditModalOpen = false;
                            this.fetchMovementData();
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error updating movement:', error);
                        Swal.fire('Error', 'Terjadi kesalahan saat memperbarui data.', 'error');
                    }
                },

                deleteMovement(id) {
                    Swal.fire({
                        title: 'Anda yakin?',
                        text: "Data ini tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d90f23',
                        cancelButtonColor: '#ff8b00',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                const response = await fetch(
                                    `controller/movement_controller.php?action=delete_movement&id=${id}`, {
                                        method: 'POST'
                                    });
                                const data = await response.json();

                                if (data.status === 'success') {
                                    Swal.fire('Dihapus!', 'Data transaksi telah dihapus.', 'success');
                                    this.fetchMovementData();
                                } else {
                                    Swal.fire('Gagal', data.message, 'error');
                                }
                            } catch (error) {
                                console.error('Error deleting movement:', error);
                                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        }
                    });
                },

                download(type) {
                    if (this.movementData.length === 0) {
                        Swal.fire('Info', 'Tidak ada data untuk diunduh.', 'info');
                        return;
                    }

                    const dataForReport = this.movementData.map((item, index) => ({
                        'No': index + 1,
                        'Item': item.item,
                        'Lokasi': item.lokasi,
                        'Group': item.group,
                        'STT/Resi': item.stt_resi || '-',
                        'EAN': item.ean || '-',
                        'IMEI/SN': item.imei || item.sn || '-',
                        'Qty': item.qty,
                        'Tanggal & Waktu': item.created_at,
                        'User': item.nama_user
                    }));

                    const dateRange = `Periode: ${this.startDate} s/d ${this.endDate}`;
                    const printTime = `Waktu Cetak: ${new Date().toLocaleString()}`;

                    if (type === 'excel') {
                        this.downloadExcel(dataForReport, 'histori_movement', 'Histori Movement', dateRange, printTime);
                    } else if (type === 'pdf') {
                        this.downloadPdf(dataForReport, 'histori_movement', 'Histori Movement', dateRange, printTime);
                    }
                },

                async downloadExcel(data, filename, title, dateRange, printTime) {
                    const workbook = new ExcelJS.Workbook();
                    const worksheet = workbook.addWorksheet(title);

                    // Add logo
                    const logoResponse = await fetch('./assets/Lion.png');
                    const logoBuffer = await logoResponse.arrayBuffer();
                    const logoId = workbook.addImage({
                        buffer: logoBuffer,
                        extension: 'png',
                    });

                    worksheet.addImage(logoId, 'A1:A2');
                    worksheet.mergeCells('B1:J1');
                    worksheet.getCell('B1').value = title;
                    worksheet.getCell('B1').font = {
                        name: 'Arial',
                        size: 20,
                        bold: true
                    };
                    worksheet.getCell('B1').alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };

                    // Add date range and print time
                    worksheet.mergeCells('B2:J2');
                    worksheet.getCell('B2').value = dateRange;
                    worksheet.getCell('B2').font = {
                        name: 'Arial',
                        size: 12,
                        italic: true
                    };
                    worksheet.getCell('B2').alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };

                    worksheet.mergeCells('B3:J3');
                    worksheet.getCell('B3').value = printTime;
                    worksheet.getCell('B3').font = {
                        name: 'Arial',
                        size: 12,
                        italic: true
                    };
                    worksheet.getCell('B3').alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };

                    // Headers
                    const headers = Object.keys(data[0]);
                    const headerRow = worksheet.addRow(headers);
                    headerRow.eachCell(cell => {
                        cell.font = {
                            bold: true
                        };
                        cell.alignment = {
                            horizontal: 'center'
                        };
                        cell.fill = {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: {
                                argb: 'FFE5E7EB'
                            }
                        }; // Light gray background
                    });

                    // Data
                    data.forEach(row => {
                        const addedRow = worksheet.addRow(Object.values(row));
                        // Align 'No' and 'Qty' columns to center
                        addedRow.getCell('A').alignment = {
                            horizontal: 'center'
                        };
                        addedRow.getCell('H').alignment = {
                            horizontal: 'center'
                        };
                    });

                    // Dynamic column width adjustment with additional padding
                    worksheet.columns.forEach((column, i) => {
                        const headerLength = headers[i].length;
                        const columnData = data.map(row => (Object.values(row)[i] || '').toString());
                        const maxLength = Math.max(...columnData.map(d => d.length), headerLength);
                        column.width = maxLength < 10 ? 12 : maxLength + 5;
                    });

                    const buffer = await workbook.xlsx.writeBuffer();
                    const blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `${filename}.xlsx`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },

                async downloadPdf(data, filename, title, dateRange, printTime) {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF('l', 'pt', 'a4'); // 'l' for landscape
                    const headers = Object.keys(data[0]);
                    const rows = data.map(item => Object.values(item));

                    // Add logo and title
                    const logoImg = new Image();
                    logoImg.src = './assets/Lion.png';
                    await new Promise(resolve => logoImg.onload = resolve);
                    doc.addImage(logoImg, 'PNG', 40, 20, 50, 50); // x, y, width, height

                    doc.setFontSize(20);
                    doc.setTextColor(40);
                    doc.text(title, doc.internal.pageSize.getWidth() / 2, 45, {
                        align: 'center'
                    });

                    doc.setFontSize(10);
                    doc.text(dateRange, doc.internal.pageSize.getWidth() / 2, 60, {
                        align: 'center'
                    });
                    doc.text(printTime, doc.internal.pageSize.getWidth() / 2, 75, {
                        align: 'center'
                    });

                    doc.autoTable({
                        head: [headers],
                        body: rows,
                        startY: 95, // Start table below the header
                        theme: 'grid',
                        styles: {
                            fontSize: 8,
                            halign: 'center'
                        },
                        headStyles: {
                            fillColor: [240, 240, 240],
                            textColor: [0, 0, 0]
                        },
                        columnStyles: {
                            'No': {
                                cellWidth: 20
                            },
                            'Qty': {
                                cellWidth: 40
                            }
                        }
                    });

                    doc.save(`${filename}.pdf`);
                },

                printSingleMovement(movement) {
                    const today = new Date().toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    const type = movement.qty > 0 ? 'Inbound' : 'Outbound';
                    // Asumsi adminName didapatkan dari data yang diteruskan ke fungsi
                    const adminName = movement.admin_name || 'Admin';

                    const printContent = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>Dokumen Serah Terima Barang</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 14px;
                color: #111827;
            }
            .print-container {
                width: 21cm;
                min-height: 29.7cm;
                margin: 0 auto;
                padding: 2cm;
                box-sizing: border-box;
            }
            .print-header {
                position: relative;
                border-bottom: 2px solid #d90f23;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            .print-header .logo-container {
                display: block;
                height: 50px;
            }
            .print-header .logo-container img {
                width: 150px;
                height: auto;
                position: absolute;
                top: 0;
                left: 0;
            }
            .print-header .header-content {
                text-align: center;
                margin-top: 50px; /* Jarak agar tidak bertabrakan dengan logo */
            }
            .print-header .header-content h1 {
                font-size: 20px;
                color: #d90f23;
                font-weight: bold;
                margin: 0;
            }
            .print-header .header-content p {
                font-size: 14px;
                margin: 0;
            }
            .print-header .header-content p span {
                font-weight: bold;
                color: #d90f23;
            }
            .detail-box {
                border: 1.5px solid #d90f23;
                border-radius: 6px;
                padding: 10px 15px;
                margin-bottom: 40px;
            }
            .detail-box h2 {
                font-size: 14px;
                font-weight: bold;
                color: #d90f23;
                margin-bottom: 10px;
            }
            .detail-table {
                width: 100%;
                border-collapse: collapse;
            }
            .detail-table td {
                padding: 6px 10px;
                text-align: left;
                border: 1px solid #e5e7eb;
                font-size: 13px;
            }
            .detail-table .label {
                font-weight: bold;
                width: 35%;
            }
            .signature-section {
                margin-top: 60px;
            }
            .signature-text-line {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }
            .signature-text-line > div {
                text-align: center;
                width: 45%;
            }
            .signature-full-line {
                border-top: 1px solid black;
                margin-top: 60px;
                margin-bottom: 5px;
            }
            .signature-names-line {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }
            .signature-names-line > div {
                text-align: center;
                width: 45%;
            }
            .signature-names-line p {
                margin: 0;
            }
        </style>
    </head>
    <body>
        <div class="print-container">
            <div class="print-header">
                <div class="logo-container">
                    <img src="./assets/Lion.png" alt="Logo">
                </div>
                <div class="header-content">
                    <h1>Dokumen Serah Terima Barang</h1>
                    <p>Tanggal: <span>${today}</span></p>
                </div>
            </div>

            <div class="detail-box">
                <h2>Detail Transaksi ${type}</h2>
                <table class="detail-table">
                    <tr><td class="label">STT/Resi</td><td>${movement.stt_resi || '-'}</td></tr>
                    <tr><td class="label">EAN</td><td>${movement.ean || '-'}</td></tr>
                    <tr><td class="label">IMEI</td><td>${movement.imei || '-'}</td></tr>
                    <tr><td class="label">SN</td><td>${movement.sn || '-'}</td></tr>
                    <tr><td class="label">Item</td><td>${movement.item || '-'}</td></tr>
                    <tr><td class="label">Jumlah (Qty)</td><td>${Math.abs(movement.qty) || '-'}</td></tr>
                    <tr><td class="label">Grouping</td><td>${movement.group || '-'}</td></tr>
                    <tr><td class="label">Lokasi</td><td>${movement.lokasi || '-'}</td></tr>
                </table>
            </div>

            <div class="signature-section">
                <div class="signature-text-line">
                    <div><p>Diserahkan,</p></div>
                    <div><p>Diterima,</p></div>
                </div>
                <div class="signature-full-line"></div>
                <div class="signature-names-line">
                    <div><p>(Admin)</p></div>
                    <div><p>( . . . . . . . . . )</p></div>
                </div>
            </div>
        </div>
    </body>
    </html>
    `;

                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(printContent);
                    printWindow.document.close();
                    printWindow.print();
                }

            }));
        });
    </script>

</body>

</html>