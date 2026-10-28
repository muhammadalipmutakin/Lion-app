<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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

    /* Styling untuk input yang disabled agar terlihat jelas */
    .input-disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    /* Styling untuk template PDF */
    #pdf-template {
        width: 210mm;
        padding: 20mm;
        background-color: #fff;
        color: #000;
        font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
        box-sizing: border-box;
        display: none;
    }

    #pdf-template .header {
        text-align: center;
        border-bottom: 2px solid #d90f23;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    #pdf-template .header h1 {
        color: #d90f23;
        font-size: 28px;
        margin-top: 10px;
    }

    #pdf-template table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    #pdf-template th,
    #pdf-template td {
        padding: 10px;
        text-align: left;
        border: 1px solid #e5e7eb;
    }

    #pdf-template .signature-area table {
        border: none;
        margin-top: 50px;
    }

    #pdf-template .signature-area td {
        border: none;
        text-align: center;
        padding: 0;
        vertical-align: top;
    }

    /* Custom styling for SweetAlert2 for full-screen preview */
    .swal2-container-full {
        z-index: 10000 !important;
        width: 100% !important;
        height: 100% !important;
        padding: 0 !important;
    }

    .swal2-popup-full {
        width: 95% !important;
        max-width: 95% !important;
        margin: 0;
        padding: 0;
    }

    /* Fix for SweetAlert2 button focus */
    .swal2-confirm.lp-red-bg:focus {
        box-shadow: 0 0 0 3px rgba(217, 15, 35, 0.5) !important;
    }
</style>


<div class="container mx-auto md:p-8 w-dvw-10">
    <h1 class="text-3xl md:text-4xl font-bold lp-red-text mb-6">
        <i class="fas fa-exchange-alt mr-2"></i>Halaman Transaksi
    </h1>

    <div x-data="transactionApp()" x-init="init()" class="bg-white p-4 md:p-6 rounded-lg shadow-md">
        <div class="mb-6 flex space-y-2 md:space-y-0 md:space-x-4">
            <button @click="activeTab = 'inbound'"
                :class="{'bg-orange-500 text-white shadow-md': activeTab === 'inbound', 'text-gray-500 hover:text-gray-700 hover:bg-gray-100': activeTab !== 'inbound'}"
                class="whitespace-nowrap py-3 px-6 rounded-md font-bold text-sm md:text-base transition-all duration-200 focus:outline-none flex items-center justify-center">
                <i class="fas fa-arrow-alt-circle-down mr-2"></i> Inbound (Masuk)
            </button>
            <button @click="activeTab = 'outbound'"
                :class="{'bg-orange-500 text-white shadow-md': activeTab === 'outbound', 'text-gray-500 hover:text-gray-700 hover:bg-gray-100': activeTab !== 'outbound'}"
                class="whitespace-nowrap py-3 px-6 rounded-md font-bold text-sm md:text-base transition-all duration-200 focus:outline-none flex items-center justify-center">
                <i class="fas fa-arrow-alt-circle-up mr-2"></i> Outbound (Keluar)
            </button>
        </div>

        <div x-show="activeTab === 'inbound'" x-transition>
            <form id="inboundForm" @submit.prevent="submitForm($event, 'inbound')" class="space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="in_stt_resi"
                            class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-barcode mr-2"></i>STT/Resi</label>
                        <input type="text" id="in_stt_resi" name="stt_resi"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200">
                    </div>
                    <div class="space-y-2">
                        <label for="in_ean" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-box mr-2"></i>EAN</label>
                        <input type="text" id="in_ean" name="ean"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200">
                    </div>
                    <div class="space-y-2">
                        <label for="in_imei" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-mobile-alt mr-2"></i>IMEI</label>
                        <input type="text" id="in_imei" name="imei"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200">
                    </div>
                    <div class="space-y-2">
                        <label for="in_sn" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-barcode mr-2"></i>Serial Number (SN)</label>
                        <input type="text" id="in_sn" name="sn"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200">
                    </div>
                    <div class="space-y-2">
                        <label for="in_item" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-tags mr-2"></i>Item</label>
                        <input type="text" id="in_item" name="item"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200"
                            required>
                    </div>
                    <div class="space-y-2">
                        <label for="in_qty" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-sort-numeric-up-alt mr-2"></i>Quantity (Qty)</label>
                        <input type="number" id="in_qty" name="qty" step="any"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200"
                            required>
                    </div>
                    <div class="space-y-2">
                        <label for="in_id_group"
                            class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-layer-group mr-2"></i>Grouping</label>
                        <select id="in_id_group" name="id_group"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Grouping</option>
                            <template x-for="group in groupOptions" :key="group.id">
                                <option :value="group.id" x-text="group.text"></option>
                            </template>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="in_id_lokasi"
                            class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-map-marker-alt mr-2"></i>Lokasi</label>
                        <select id="in_id_lokasi" name="id_lokasi"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Lokasi</option>
                            <template x-for="lokasi in lokasiOptions" :key="lokasi.id">
                                <option :value="lokasi.id" x-text="lokasi.text"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <button type="submit"
                    class="w-full lp-red-bg text-white py-3 px-4 rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-plus-circle mr-2"></i>Simpan Transaksi Inbound
                </button>
            </form>
        </div>

        <div x-show="activeTab === 'outbound'" x-transition>
            <form id="outboundForm" @submit.prevent="submitForm($event, 'outbound')" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4 mb-4">
                    <div class="flex-1 space-y-2">
                        <label for="out_stt_resi_search"
                            class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-search mr-2"></i>Cari Berdasarkan STT/Resi</label>
                        <div class="flex">
                            <input type="search" id="out_stt_resi_search" name="stt_resi_search"
                                class="w-full p-2 border border-gray-300 rounded-l-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200"
                                placeholder="Masukkan STT/Resi..."
                                @keydown.enter.prevent="searchOutboundByResi($event.target.value)" inputmode="search"
                                enterkeyhint="search">
                            <button type="button" @click="searchOutboundByResi($el.previousElementSibling.value)"
                                class="w-1/2 lp-orange-bg text-white py-2 px-4 rounded-r-md font-semibold hover:bg-orange-600 transition-colors duration-200">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="out_stt_resi"
                            class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-barcode mr-2"></i>STT/Resi</label>
                        <input type="text" id="out_stt_resi" name="stt_resi"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm input-disabled" readonly>
                    </div>
                    <div class="space-y-2">
                        <label for="out_ean" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-box mr-2"></i>EAN</label>
                        <input type="text" id="out_ean" name="ean"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm input-disabled" readonly>
                    </div>
                    <div class="space-y-2">
                        <label for="out_imei" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-mobile-alt mr-2"></i>IMEI</label>
                        <input type="text" id="out_imei" name="imei"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm input-disabled" readonly>
                    </div>
                    <div class="space-y-2">
                        <label for="out_sn" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-barcode mr-2"></i>Serial Number (SN)</label>
                        <input type="text" id="out_sn" name="sn"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm input-disabled" readonly>
                    </div>
                    <div class="space-y-2">
                        <label for="out_item" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-tags mr-2"></i>Item</label>
                        <input type="text" id="out_item" name="item"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm input-disabled" readonly>
                    </div>
                    <div class="space-y-2">
                        <label for="out_id_lokasi"
                            class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-map-marker-alt mr-2"></i>Lokasi</label>
                        <input type="text" id="out_id_lokasi" name="lokasi_text"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm input-disabled" readonly>
                        <input type="hidden" name="id_lokasi" id="out_id_lokasi_val">
                    </div>
                    <div class="space-y-2">
                        <label for="out_id_group"
                            class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-layer-group mr-2"></i>Grouping</label>
                        <input type="text" id="out_id_group" name="group_text"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm input-disabled" readonly>
                        <input type="hidden" name="id_group" id="out_id_group_val">
                    </div>
                    <div class="space-y-2">
                        <label for="out_qty" class="block text-sm md:text-base font-medium text-gray-700"><i
                                class="fas fa-sort-numeric-down-alt mr-2"></i>Quantity (Qty)</label>
                        <input type="number" id="out_qty" name="qty" step="any"
                            class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-lp-orange-bg focus:border-lp-orange-bg transition-colors duration-200"
                            required>
                    </div>
                </div>
                <button type="submit" id="outbound-submit-btn"
                    class="w-full lp-red-bg text-white py-3 px-4 rounded-md font-semibold hover:bg-red-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                    <i class="fas fa-minus-circle mr-2"></i>Simpan Transaksi Outbound
                </button>
            </form>
        </div>
    </div>
</div>

<div id="pdf-template">
    <div class="header">
        <img src="./assets/lion.png" alt="Lion Parcel Logo"
            style="height: 40px;" />
        <h1>Dokumen Serah Terima Barang</h1>
        <p style="font-size: 14px; margin-top: 5px;">Tanggal: <b id="pdf-date"></b></p>
    </div>
    <div style="border: 1px solid #d90f23; padding: 15px; margin-bottom: 25px; border-radius: 8px;">
        <h3 style="font-size: 18px; color: #d90f23; margin-bottom: 15px;">Detail Transaksi Outbound</h3>
        <table style="border: none;">
            <tr>
                <td style="width: 30%; font-weight: bold;">STT/Resi</td>
                <td id="pdf-stt-resi"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">EAN</td>
                <td id="pdf-ean"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">IMEI</td>
                <td id="pdf-imei"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">SN</td>
                <td id="pdf-sn"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Item</td>
                <td id="pdf-item"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jumlah (Qty)</td>
                <td id="pdf-qty"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Grouping</td>
                <td id="pdf-group"></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Lokasi</td>
                <td id="pdf-lokasi"></td>
            </tr>
        </table>
    </div>
    <div class="signature-area">
        <table width="100%">
            <tr>
                <td align="center" style="width: 50%;">
                    <p style="font-size: 14px;">Diserahkan,</p>
                    <div style="height: 60px;"></div>
                    <p style="font-weight: bold; border-top: 1px solid #000; padding-top: 5px;">(<span
                            id="pdf-user"></span>)</p>
                </td>
                <td align="center" style="width: 50%;">
                    <p style="font-size: 14px;">Diterima,</p>
                    <div style="height: 60px;"></div>
                    <p style="font-weight: bold; border-top: 1px solid #000; padding-top: 5px;">( . . . . . . . . . .
                        . . )</p>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    const {
        jsPDF
    } = window.jspdf;

    document.addEventListener('alpine:init', () => {
        Alpine.data('transactionApp', () => ({
            activeTab: 'inbound',
            groupOptions: [],
            lokasiOptions: [],
            init() {
                this.fetchDropdownData();
            },
            async fetchDropdownData() {
                try {
                    const response = await fetch('controller/transaksi_controller.php?action=get_dropdown_data');
                    const data = await response.json();
                    if (data.status === 'success') {
                        this.groupOptions = data.group.map(item => ({
                            id: item.id,
                            text: item.group
                        }));
                        this.lokasiOptions = data.lokasi.map(item => ({
                            id: item.id,
                            text: item.lokasi
                        }));
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error fetching dropdown data:', error);
                    Swal.fire('Error', 'Gagal memuat data dropdown.', 'error');
                }
            },
            async searchOutboundByResi(resi) {
                const searchInput = document.getElementById('out_stt_resi_search');
                if (!resi) {
                    Swal.fire('Peringatan', 'Silakan masukkan nomor STT/Resi.', 'warning');
                    searchInput.focus();
                    return;
                }
                this.resetOutboundForm(false);

                try {
                    Swal.fire({
                        title: 'Mencari...',
                        text: 'Mohon tunggu, data sedang dicari.',
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        customClass: {
                            container: 'swal2-container-centered'
                        }
                    });

                    const response = await fetch(
                        `controller/transaksi_controller.php?action=search_by_resi&resi=${resi}`
                    );
                    const data = await response.json();
                    Swal.close();

                    if (data.status === 'success' && data.data) {
                        const formData = data.data;
                        document.getElementById('out_stt_resi').value = formData.stt_resi;
                        document.getElementById('out_ean').value = formData.ean;
                        document.getElementById('out_imei').value = formData.imei;
                        document.getElementById('out_sn').value = formData.sn;
                        document.getElementById('out_item').value = formData.item;
                        document.getElementById('out_id_lokasi_val').value = formData.id_lokasi;
                        document.getElementById('out_id_lokasi').value = formData.lokasi;
                        document.getElementById('out_id_group_val').value = formData.id_group;
                        document.getElementById('out_id_group').value = formData.group;
                        document.getElementById('out_qty').value = formData.qty;
                        document.getElementById('out_qty').max = formData.qty;
                        document.getElementById('outbound-submit-btn').disabled = false;
                        searchInput.blur(); // Lepaskan fokus saat berhasil
                    } else {
                        Swal.fire('Tidak Ditemukan',
                            'Data untuk STT/Resi tersebut tidak ada atau stok kosong.', 'info');
                        this.resetOutboundForm(true);
                        searchInput.focus(); // Fokus kembali jika gagal
                    }
                } catch (error) {
                    console.error('Error searching by resi:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat mencari data.', 'error');
                    this.resetOutboundForm(true);
                    searchInput.focus();
                }
            },
            async submitForm(e, jenis) {
                const form = e.target;
                const formData = new FormData(form);

                formData.append('jenis', jenis);
                formData.append('id_user', '<?php echo $_SESSION['user_data']['id']; ?>');

                // --- Mengatur waktu agar sesuai dengan zona waktu Jakarta (+7 jam dari UTC) ---
                const now = new Date();
                now.setHours(now.getHours() + 7); // Tambahkan 7 jam

                const mysqlTimestamp = now.toISOString().slice(0, 19).replace('T', ' ');
                formData.append('timestamp', mysqlTimestamp);

                // --- LOGGING UNTUK DEBUGGING ---
                const formObject = {};
                formData.forEach((value, key) => {
                    formObject[key] = value;
                });
                console.log('Data yang akan dikirim ke backend:', formObject);
                // -------------------------------

                if (jenis === 'outbound') {
                    const requestedQty = parseFloat(formData.get('qty'));
                    const maxQty = parseFloat(document.getElementById('out_qty').max);
                    if (requestedQty <= 0 || requestedQty > maxQty) {
                        Swal.fire('Gagal!',
                            `Jumlah yang diminta tidak valid atau melebihi stok yang tersedia (${maxQty}).`,
                            'error');
                        return;
                    }
                }

                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonHtml = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...`;

                try {
                    const response = await fetch('controller/transaksi_controller.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        if (jenis === 'outbound') {
                            await this.handleOutboundSuccess(data.data, form);
                        } else {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success'
                            }).then(() => {
                                this.resetForm(form);
                            });
                        }
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat mengirim data.', 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonHtml;
                }
            },
            async handleOutboundSuccess(transactionData, form) {
                const isMobile = window.innerWidth < 768;

                if (isMobile) {
                    Swal.fire({
                        title: 'Transaksi Berhasil!',
                        html: `<p>Dokumen serah terima telah dibuat.</p>`,
                        icon: 'success',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Cetak Dokumen',
                        denyButtonText: 'Lihat Dokumen',
                        cancelButtonText: 'Tutup',
                        confirmButtonClass: 'lp-red-bg',
                        denyButtonClass: 'lp-orange-bg',
                        didClose: () => {
                            this.resetForm(form);
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.generatePdfAndDownload(transactionData);
                        } else if (result.isDenied) {
                            this.generatePdfAndOpen(transactionData);
                        }
                    });
                } else {
                    await this.createAndShowPdf(transactionData, form);
                }
            },
            async createAndShowPdf(transactionData, form) {
                Swal.fire({
                    title: 'Membuat Dokumen...',
                    text: 'Mohon tunggu, dokumen sedang diproses.',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    customClass: {
                        container: 'swal2-container-centered'
                    }
                });

                document.getElementById('pdf-date').textContent = new Date().toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                document.getElementById('pdf-stt-resi').textContent = transactionData.stt_resi || '-';
                document.getElementById('pdf-ean').textContent = transactionData.ean || '-';
                document.getElementById('pdf-imei').textContent = transactionData.imei || '-';
                document.getElementById('pdf-sn').textContent = transactionData.sn || '-';
                document.getElementById('pdf-item').textContent = transactionData.item || '-';
                document.getElementById('pdf-qty').textContent = Math.abs(transactionData.qty);
                document.getElementById('pdf-group').textContent = transactionData.group || '-';
                document.getElementById('pdf-lokasi').textContent = transactionData.lokasi || '-';
                document.getElementById('pdf-user').textContent = transactionData.user || '-';

                const pdfTemplate = document.getElementById('pdf-template');
                pdfTemplate.style.display = 'block';

                try {
                    const canvas = await html2canvas(pdfTemplate, {
                        scale: 2
                    });
                    const imgData = canvas.toDataURL('image/jpeg', 1.0);
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                    pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
                    const pdfUrl = pdf.output('bloburl');

                    Swal.close();

                    Swal.fire({
                        title: 'Dokumen Serah Terima',
                        html: `<iframe src="${pdfUrl}" style="width:100%; height:80vh; border:none;"></iframe>`,
                        width: '95%',
                        showCancelButton: true,
                        confirmButtonText: 'Cetak Dokumen',
                        cancelButtonText: 'Tutup',
                        allowOutsideClick: false,
                        showCloseButton: true,
                        customClass: {
                            container: 'swal2-container-full',
                            popup: 'swal2-popup-full',
                            confirmButton: 'lp-red-bg'
                        },
                        didClose: () => {
                            this.resetForm(form);
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(pdfUrl, '_blank');
                        }
                    });
                } catch (error) {
                    console.error('Error generating PDF:', error);
                    Swal.fire('Error', 'Gagal membuat dokumen PDF.', 'error');
                } finally {
                    pdfTemplate.style.display = 'none';
                }
            },
            async generatePdfAndDownload(transactionData) {
                // Same logic as before to generate PDF
                document.getElementById('pdf-date').textContent = new Date().toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                document.getElementById('pdf-stt-resi').textContent = transactionData.stt_resi || '-';
                document.getElementById('pdf-ean').textContent = transactionData.ean || '-';
                document.getElementById('pdf-imei').textContent = transactionData.imei || '-';
                document.getElementById('pdf-sn').textContent = transactionData.sn || '-';
                document.getElementById('pdf-item').textContent = transactionData.item || '-';
                document.getElementById('pdf-qty').textContent = Math.abs(transactionData.qty);
                document.getElementById('pdf-group').textContent = transactionData.group || '-';
                document.getElementById('pdf-lokasi').textContent = transactionData.lokasi || '-';
                document.getElementById('pdf-user').textContent = transactionData.user || '-';

                const pdfTemplate = document.getElementById('pdf-template');
                pdfTemplate.style.display = 'block';

                try {
                    const canvas = await html2canvas(pdfTemplate, {
                        scale: 2
                    });
                    const imgData = canvas.toDataURL('image/jpeg', 1.0);
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                    pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save(`Dokumen-Serah-Terima-${transactionData.stt_resi}.pdf`);
                } catch (error) {
                    console.error('Error generating PDF:', error);
                    Swal.fire('Error', 'Gagal membuat dokumen PDF.', 'error');
                } finally {
                    pdfTemplate.style.display = 'none';
                }
            },
            async generatePdfAndOpen(transactionData) {
                // Same logic as before to generate PDF and open in new tab
                document.getElementById('pdf-date').textContent = new Date().toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                document.getElementById('pdf-stt-resi').textContent = transactionData.stt_resi || '-';
                document.getElementById('pdf-ean').textContent = transactionData.ean || '-';
                document.getElementById('pdf-imei').textContent = transactionData.imei || '-';
                document.getElementById('pdf-sn').textContent = transactionData.sn || '-';
                document.getElementById('pdf-item').textContent = transactionData.item || '-';
                document.getElementById('pdf-qty').textContent = Math.abs(transactionData.qty);
                document.getElementById('pdf-group').textContent = transactionData.group || '-';
                document.getElementById('pdf-lokasi').textContent = transactionData.lokasi || '-';
                document.getElementById('pdf-user').textContent = transactionData.user || '-';

                const pdfTemplate = document.getElementById('pdf-template');
                pdfTemplate.style.display = 'block';

                try {
                    const canvas = await html2canvas(pdfTemplate, {
                        scale: 2
                    });
                    const imgData = canvas.toDataURL('image/jpeg', 1.0);
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                    pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
                    const pdfUrl = pdf.output('bloburl');
                    window.open(pdfUrl, '_blank');
                } catch (error) {
                    console.error('Error generating PDF:', error);
                    Swal.fire('Error', 'Gagal membuat dokumen PDF.', 'error');
                } finally {
                    pdfTemplate.style.display = 'none';
                }
            },
            resetForm(form) {
                form.reset();
                if (form.id === 'outboundForm') {
                    this.resetOutboundForm(true);
                }
            },
            resetOutboundForm(fullReset = true) {
                const form = document.getElementById('outboundForm');
                if (fullReset) {
                    document.getElementById('out_stt_resi_search').value = '';
                }
                document.getElementById('out_stt_resi').value = '';
                document.getElementById('out_ean').value = '';
                document.getElementById('out_imei').value = '';
                document.getElementById('out_sn').value = '';
                document.getElementById('out_item').value = '';
                document.getElementById('out_id_lokasi').value = '';
                document.getElementById('out_id_lokasi_val').value = '';
                document.getElementById('out_id_group').value = '';
                document.getElementById('out_id_group_val').value = '';
                document.getElementById('out_qty').value = '';
                document.getElementById('out_qty').max = '';
                document.getElementById('outbound-submit-btn').disabled = true;
            }
        }));
    });
</script>