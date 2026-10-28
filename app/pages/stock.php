<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>



<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 4px 8px;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
    }

    .select2-container .select2-selection--multiple {
        min-height: 38px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 6px !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .modal-table-container {
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-table-container table {
        width: 100%;
        table-layout: fixed;
    }
</style>

<div class="container mx-auto p-4 md:p-8">
    <h1 class="text-3xl md:text-4xl font-bold text-[#d90f23] mb-6">
        <i class="fas fa-box-open mr-2"></i>Laporan Stok
    </h1>

    <div x-data="stockApp()" x-init="init()" class="bg-white p-4 md:p-6 rounded-lg shadow-md">
        <div class="mb-6 grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                <input type="date" id="start_date" x-model="startDate" @change="fetchStockData()"
                    class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#ff8b00] focus:border-[#ff8b00]">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                <input type="date" id="end_date" x-model="endDate" @change="fetchStockData()"
                    class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-[#ff8b00] focus:border-[#ff8b00]">
            </div>
            <div>
                <label for="filter_item" class="block text-sm font-medium text-gray-700">Item</label>
                <select id="filter_item" class="w-full" multiple="multiple">
                </select>
            </div>
            <div>
                <label for="filter_lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                <select id="filter_lokasi" class="w-full" multiple="multiple">
                </select>
            </div>
            <div>
                <label for="filter_group" class="block text-sm font-medium text-gray-700">Grouping</label>
                <select id="filter_group" class="w-full" multiple="multiple">
                </select>
            </div>
            <div class="col-span-2 md:col-span-1">
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

        <p class="text-sm text-gray-600 mb-4" x-show="stockData.length > 0">
            Data ditampilkan dari tanggal **<span x-text="startDate"></span>** hingga **<span
                x-text="endDate"></span>**.
        </p>

        <div class="overflow-x-auto relative max-h-[60vh]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Item</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#eff6ff]">
                            Stok Awal</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#f0fdf4]">
                            Masuk</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#fef2f2]">
                            Keluar</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-[#fffbeb]">
                            Stok Akhir</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr x-show="stockData.length > 0"
                        class="hover:bg-gray-100 sticky bottom-0 z-20 bg-gray-200 font-bold">
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900" colspan="2">Total</td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 bg-[#eff6ff]"
                            x-text="calculateTotal('stok_awal')"></td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-green-600 bg-[#f0fdf4]"
                            x-text="calculateTotal('masuk')"></td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-red-600 bg-[#fef2f2]"
                            x-text="calculateTotal('keluar')"></td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 bg-[#fffbeb]"
                            x-text="calculateTotal('stok_akhir')"></td>
                        <td class="px-6 py-3 whitespace-nowrap"></td>
                    </tr>

                    <template x-for="(stock, index) in stockData" :key="index">
                        <tr x-data="{isHovering: false}" @mouseenter="isHovering = true"
                            @mouseleave="isHovering = false"
                            :class="{'bg-red-100': stock.stok_akhir < 0, 'bg-gray-100': isHovering}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="index + 1"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="stock.item"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                :class="{'bg-[#eff6ff]': !isHovering}" x-text="stock.stok_awal"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium"
                                :class="{'bg-[#f0fdf4]': !isHovering}" x-text="stock.masuk"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium"
                                :class="{'bg-[#fef2f2]': !isHovering}" x-text="stock.keluar"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold"
                                :class="{'bg-[#fffbeb]': !isHovering, 'text-red-600': stock.stok_akhir < 0, 'text-gray-900': stock.stok_akhir >= 0}"
                                x-text="stock.stok_akhir">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button @click="openDetailTransaksiModal(stock.item)"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-file-invoice mr-1"></i> Transaksi
                                </button>
                                <button @click="openDetailStokModal(stock.item)"
                                    class="text-[#ff8b00] hover:text-orange-700">
                                    <i class="fas fa-cubes mr-1"></i> Stok
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="stockData.length === 0" class="hover:bg-gray-100">
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data stok ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div x-show="isTransaksiModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isTransaksiModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="isTransaksiModalOpen"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-[#d90f23] px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                            Detail Transaksi untuk <span x-text="selectedItem.item"></span>
                        </h3>
                        <button @click="isTransaksiModalOpen = false"
                            class="text-white hover:text-gray-300 text-xl leading-none">
                            <i class="fas fa-times text-2xl text-white"></i>
                        </button>
                    </div>
                    <div class="p-4 sm:p-6 bg-white">
                        <div class="modal-table-container border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200" style="table-layout: auto;">
                                <thead class="sticky top-0 z-10 bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[5%]">
                                            No</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                            Tanggal & Waktu</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                            Jenis</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                            User</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                            STT/Resi</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">
                                            EAN</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">
                                            IMEI/SN</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">
                                            Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(trans, tIndex) in selectedItem.transactions" :key="tIndex">
                                        <tr :class="{'bg-[#d1f7e3]': trans.qty > 0, 'bg-[#fce7e7]': trans.qty < 0}"
                                            class="hover:bg-gray-100">
                                            <td class="px-3 py-4 text-sm text-gray-900" x-text="tIndex + 1"></td>
                                            <td class="px-3 py-4 text-sm text-gray-900" x-text="trans.created_at">
                                            </td>
                                            <td class="px-3 py-4 text-sm font-semibold" x-text="trans.jenis"></td>
                                            <td class="px-3 py-4 text-sm text-gray-900" x-text="trans.nama_user">
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-900" x-text="trans.stt_resi || '-'">
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-900" x-text="trans.ean || '-'"></td>
                                            <td class="px-3 py-4 text-sm text-gray-900"
                                                x-text="trans.imei || trans.sn || '-'"></td>
                                            <td :class="{'text-green-600': trans.qty > 0, 'text-red-600': trans.qty < 0}"
                                                class="px-3 py-4 text-sm font-medium"
                                                x-text="trans.qty > 0 ? '+' + trans.qty : trans.qty"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="selectedItem.transactions.length === 0" class="hover:bg-gray-100">
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada
                                            riwayat transaksi.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="isStokModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="stok-modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isStokModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="isStokModalOpen"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-[#ff8b00] px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-white" id="stok-modal-title">
                            Stok Saat Ini: <span x-text="selectedStok.item"></span>
                        </h3>
                        <button @click="isStokModalOpen = false"
                            class="text-white hover:text-gray-300 text-xl leading-none">
                            <i class="fas fa-times text-2xl text-white"></i>
                        </button>
                    </div>
                    <div class="p-4 sm:p-6 bg-white">
                        <div class="modal-table-container border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="sticky top-0 z-10 bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Group</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(stokDetail, sdIndex) in selectedStok.details"
                                        :key="sdIndex">
                                        <tr :class="{'bg-[#fce7e7]': stokDetail.stok_akhir < 0}"
                                            class="hover:bg-gray-100">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                                x-text="sdIndex + 1"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                                x-text="stokDetail.lokasi"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                                x-text="stokDetail.group"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold"
                                                x-text="stokDetail.stok_akhir"
                                                :class="{'text-red-600': stokDetail.stok_akhir < 0, 'text-gray-900': stokDetail.stok_akhir >= 0}">
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="selectedStok.details.length === 0" class="hover:bg-gray-100">
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada detail
                                            stok untuk item ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('stockApp', () => ({
            stockData: [],
            allGroups: [],
            allLocations: [],
            allItems: [],
            startDate: '',
            endDate: '',
            filterItem: [],
            filterLokasi: [],
            filterGroup: [],
            isTransaksiModalOpen: false,
            isStokModalOpen: false,
            selectedItem: {
                item: '',
                transactions: []
            },
            selectedStok: {
                item: '',
                details: []
            },
            logoBase64: null,

            init() {
                const today = this.getLocalDateString();
                const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
                this.startDate = firstDayOfMonth.toISOString().split('T')[0];
                this.endDate = today;

                this.fetchDropdownData().then(() => {
                    this.setupSelect2();
                    this.fetchStockData();
                    this.fetchLogo();
                });
            },

            getLocalDateString() {
                const date = new Date();
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },

            async fetchDropdownData() {
                try {
                    const response = await fetch('controller/stock_controller.php?action=get_dropdown_data');
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

            setupSelect2() {
                this.populateSelect2('filter_item', this.allItems, 'item');
                this.populateSelect2('filter_lokasi', this.allLocations, 'lokasi');
                this.populateSelect2('filter_group', this.allGroups, 'group');

                $('#filter_item').on('change', (e) => {
                    this.filterItem = $(e.currentTarget).val() || [];
                    this.fetchStockData();
                });
                $('#filter_lokasi').on('change', (e) => {
                    this.filterLokasi = $(e.currentTarget).val() || [];
                    this.fetchStockData();
                });
                $('#filter_group').on('change', (e) => {
                    this.filterGroup = $(e.currentTarget).val() || [];
                    this.fetchStockData();
                });
            },

            populateSelect2(id, data, textKey) {
                const options = [{
                    id: '',
                    text: `Semua ${textKey.charAt(0).toUpperCase() + textKey.slice(1)}`
                }];
                data.forEach(item => {
                    options.push({
                        id: item.id || item[textKey],
                        text: item[textKey]
                    });
                });
                $(`#${id}`).empty().select2({
                    data: options,
                    theme: 'default'
                });
            },

            async fetchStockData() {
                const itemParam = this.filterItem.join(',');
                const lokasiParam = this.filterLokasi.join(',');
                const groupParam = this.filterGroup.join(',');

                const url =
                    `controller/stock_controller.php?action=get_stock_data&start_date=${this.startDate}&end_date=${this.endDate}&item=${itemParam}&lokasi=${lokasiParam}&group=${groupParam}`;
                try {
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.status === 'success') {
                        this.stockData = data.stock;
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error fetching stock data:', error);
                }
            },

            calculateTotal(column) {
                return this.stockData.reduce((sum, item) => sum + item[column], 0);
            },

            async openDetailTransaksiModal(item) {
                try {
                    const url =
                        `controller/stock_controller.php?action=get_transaction_details&item=${item}&start_date=${this.startDate}&end_date=${this.endDate}`;
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.status === 'success') {
                        this.selectedItem = {
                            item: item,
                            transactions: data.transactions
                        };
                        this.isTransaksiModalOpen = true;
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error fetching transaction details:', error);
                }
            },

            async openDetailStokModal(item) {
                try {
                    const url = `controller/stock_controller.php?action=get_stock_details&item=${item}`;
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.status === 'success') {
                        this.selectedStok = {
                            item: item,
                            details: data.details
                        };
                        this.isStokModalOpen = true;
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error fetching stock details:', error);
                }
            },

            download(type) {
                if (this.stockData.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diunduh.', 'info');
                    return;
                }

                const dataForReport = this.stockData.map((item, index) => ({
                    'No': index + 1,
                    'Item': item.item,
                    'Stok Awal': item.stok_awal,
                    'Masuk': item.masuk,
                    'Keluar': item.keluar,
                    'Stok Akhir': item.stok_akhir
                }));

                if (type === 'excel') {
                    this.downloadExcel(dataForReport, 'laporan_stok', 'Laporan Stok Barang');
                } else if (type === 'pdf') {
                    this.downloadPdf(dataForReport, 'laporan_stok', 'Laporan Stok Barang');
                }
            },

            async fetchLogo() {
                try {
                    const response = await fetch('./assets/Lion.png');
                    const blob = await response.blob();
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        this.logoBase64 = reader.result;
                    };
                    reader.readAsDataURL(blob);
                } catch (error) {
                    console.error('Gagal mengambil logo:', error);
                }
            },

            async downloadExcel(data, filename, title) {
                if (!data || data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diunduh.', 'info');
                    return;
                }

                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet(title);

                if (this.logoBase64) {
                    const logoImageId = workbook.addImage({
                        base64: this.logoBase64,
                        extension: 'png',
                    });
                    worksheet.addImage(logoImageId, {
                        tl: {
                            col: 0,
                            row: 0
                        },
                        ext: {
                            width: 120,
                            height: 60
                        }
                    });
                }

                worksheet.mergeCells('B1:F1');
                worksheet.getCell('B1').value = title;
                worksheet.getCell('B1').alignment = {
                    horizontal: 'center',
                    vertical: 'middle'
                };
                worksheet.getCell('B1').font = {
                    bold: true,
                    size: 24,
                    name: 'Calibri'
                };
                worksheet.mergeCells('B2:F2');
                worksheet.getCell('B2').value = `Laporan Stok dari ${this.startDate} s/d ${this.endDate}`;
                worksheet.getCell('B2').alignment = {
                    horizontal: 'center',
                    vertical: 'middle'
                };
                worksheet.getCell('B2').font = {
                    italic: true,
                    size: 12
                };
                worksheet.mergeCells('B3:F3');
                worksheet.getCell('B3').value = `Dicetak: ${new Date().toLocaleString()}`;
                worksheet.getCell('B3').alignment = {
                    horizontal: 'center',
                    vertical: 'middle'
                };
                worksheet.getCell('B3').font = {
                    italic: true,
                    size: 10
                };

                worksheet.addRow([]);

                const headers = Object.keys(data[0]);
                const headerRow = worksheet.addRow(headers);
                headerRow.eachCell(cell => {
                    cell.font = {
                        bold: true
                    };
                    cell.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: {
                            argb: 'FFF0F0F0'
                        }
                    };
                    cell.border = {
                        top: {
                            style: 'thin'
                        },
                        left: {
                            style: 'thin'
                        },
                        bottom: {
                            style: 'thin'
                        },
                        right: {
                            style: 'thin'
                        }
                    };
                });

                data.forEach(item => {
                    const rowData = Object.values(item);
                    const row = worksheet.addRow(rowData);
                    row.eachCell(cell => {
                        cell.border = {
                            top: {
                                style: 'thin'
                            },
                            left: {
                                style: 'thin'
                            },
                            bottom: {
                                style: 'thin'
                            },
                            right: {
                                style: 'thin'
                            }
                        };
                    });
                });

                worksheet.columns.forEach((column, index) => {
                    const headerName = headers[index];
                    if (['Stok Awal', 'Masuk', 'Keluar', 'Stok Akhir'].includes(headerName)) {
                        column.width = 15;
                        column.alignment = {
                            horizontal: 'center'
                        };
                    } else {
                        let maxLength = 0;
                        column.eachCell({
                            includeEmpty: true
                        }, cell => {
                            const columnLength = (cell.value ? cell.value.toString().length : 10);
                            if (columnLength > maxLength) {
                                maxLength = columnLength;
                            }
                        });
                        column.width = maxLength < 10 ? 10 : maxLength > 30 ? 30 : maxLength;
                    }
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

            async downloadPdf(data, filename, title) {
                if (!data || data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diunduh.', 'info');
                    return;
                }

                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('p', 'pt', 'a4');

                if (this.logoBase64) {
                    doc.addImage(this.logoBase64, 'PNG', 40, 30, 80, 40);
                }

                doc.setFontSize(22);
                doc.text(title, 297.5, 45, null, null, 'center');
                doc.setFontSize(12);
                doc.text(`Laporan Stok dari ${this.startDate} s/d ${this.endDate}`, 297.5, 65, null, null, 'center');
                doc.setFontSize(10);
                doc.text(`Dicetak: ${new Date().toLocaleString()}`, 297.5, 80, null, null, 'center');

                const tableHeaders = Object.keys(data[0]);
                const tableRows = data.map(item => Object.values(item));

                doc.autoTable({
                    head: [tableHeaders],
                    body: tableRows,
                    startY: 100,
                    styles: {
                        fontSize: 8,
                        cellPadding: 5,
                        valign: 'middle',
                        halign: 'center'
                    },
                    headStyles: {
                        fillColor: [240, 240, 240],
                        textColor: [0, 0, 0],
                        fontStyle: 'bold'
                    },
                    didDrawCell: (data) => {
                        if (data.column.index === 5 && data.cell.section === 'body') {
                            const finalStock = parseFloat(data.cell.text[0]);
                            if (finalStock < 0) {
                                doc.setTextColor(255, 0, 0);
                            } else {
                                doc.setTextColor(0, 0, 0);
                            }
                        } else if (data.column.index === 3 && data.cell.section === 'body') {
                            doc.setTextColor(0, 128, 0);
                        } else if (data.column.index === 4 && data.cell.section === 'body') {
                            doc.setTextColor(255, 0, 0);
                        } else {
                            doc.setTextColor(0, 0, 0);
                        }
                    }
                });
                doc.save(`${filename}.pdf`);
            }
        }));
    });
</script>