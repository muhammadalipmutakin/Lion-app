<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .card {
        @apply bg-white p-6 rounded-xl shadow-md;
    }

    .chart-container {
        @apply flex flex-col lg:flex-row gap-6 mb-10;
    }

    /* Memastikan canvas chart bisa merespons dengan baik di dalam container */
    .chart-canvas-wrapper {
        @apply h-80 w-full;
    }
</style>

<main class="w-full px-4 py-8 bg-white">
    <header class="text-center mb-10">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 mt-2">Ringkasan stok dan pergerakan barang</p>
    </header>


    <div id="metric-cards" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-10">
        <div class="card bg-orange-400 p-6 rounded-2xl text-white">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium">Total Stok Barang</p>
                <i class="fa-solid fa-boxes-stacked fa-2xl text-white"></i>
            </div>
            <h2 id="total-qty" class="text-3xl font-bold mt-2 animate-pulse">Memuat...</h2>
        </div>

        <div class="card bg-orange-400 p-6 rounded-2xl text-white">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium">Transaksi Masuk</p>
                <i class="fa-solid fa-arrow-right-from-bracket fa-2xl text-white"></i>
            </div>
            <h2 id="total-in" class="text-3xl font-bold mt-2 animate-pulse">Memuat...</h2>
        </div>

        <div class="card bg-orange-400 p-6 rounded-2xl text-white">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium">Transaksi Keluar</p>
                <i class="fa-solid fa-arrow-right-to-bracket fa-2xl text-white"></i>
            </div>
            <h2 id="total-out" class="text-3xl font-bold mt-2 animate-pulse">Memuat...</h2>
        </div>
    </div>

    <div class="chart-container flex flex-col lg:flex-row gap-6 mb-10 gap-x-4">
        <div class="card flex-1 bg-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Total Qty Berdasarkan Lokasi</h3>
            <div class="chart-canvas-wrapper h-80">
                <canvas id="qtyByLokasiChart"></canvas>
            </div>
        </div>

        <div class="card flex-1 bg-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 5 Stok Barang</h3>
            <div class="chart-canvas-wrapper h-80">
                <canvas id="qtyByItemChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-10">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">10 Transaksi Terbaru</h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-white bg-orange-400 uppercase tracking-wider">
                            Tanggal</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-white bg-orange-400 uppercase tracking-wider">
                            Item</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-white bg-orange-400 uppercase tracking-wider">
                            Qty</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-white bg-orange-400 uppercase tracking-wider">
                            Lokasi</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-white bg-orange-400 uppercase tracking-wider">
                            Group</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-white bg-orange-400 uppercase tracking-wider">
                            User</th>
                    </tr>
                </thead>
                <tbody id="recent-transactions-body" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-white bg-orange-400 animate-pulse">Memuat
                            data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    // Custom modal to replace alert()
    function showMessage(message) {
        const modalHtml = `
            <div id="custom-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 md:max-w-md">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi</h4>
                    <p class="text-gray-600 mb-6">${message}</p>
                    <div class="flex justify-end">
                        <button onclick="closeModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tutup</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function closeModal() {
        const modal = document.getElementById('custom-modal');
        if (modal) {
            modal.remove();
        }
    }

    window.onload = function() {
        fetchDashboardData();
    };

    async function fetchDashboardData() {
        try {
            // Fetch data from the PHP backend API
            const response = await fetch('controller/dashboard_controller.php');

            // Check if the network request was successful
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();

            if (result.status === 'success') {
                const data = result.data;

                // Update metric cards
                document.getElementById('total-qty').textContent = data.total_qty;
                document.getElementById('total-in').textContent = data.total_in;
                document.getElementById('total-out').textContent = data.total_out;

                // Render Qty by Lokasi Chart
                renderQtyByLokasiChart(data.qty_by_lokasi);

                // Render Qty by Item Chart
                renderQtyByItemChart(data.qty_by_item);

                // Populate recent transactions table
                populateRecentTransactions(data.recent_transactions);
            } else {
                console.error('Error fetching data:', result.message);
                showMessage('Gagal memuat data dashboard: ' + result.message);
            }
        } catch (error) {
            console.error('Network error:', error);
            showMessage('Terjadi kesalahan jaringan saat memuat data.');
        }
    }

    function renderQtyByLokasiChart(chartData) {
        const labels = chartData.map(d => d.lokasi);
        const values = chartData.map(d => d.total_qty_lokasi);
        const ctx = document.getElementById('qtyByLokasiChart').getContext('2d');
        // Menghancurkan instance chart yang ada sebelum membuat yang baru
        if (window.myBarChart) {
            window.myBarChart.destroy();
        }
        window.myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Qty per Lokasi',
                    data: values,
                    backgroundColor: '#6366f1',
                    borderColor: '#4f46e5',
                    borderWidth: 1,
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Set to false so the wrapper can control the size
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    }

    function renderQtyByItemChart(chartData) {
        const labels = chartData.map(d => d.item);
        const values = chartData.map(d => d.total_qty_item);
        const ctx = document.getElementById('qtyByItemChart').getContext('2d');
        // Menghancurkan instance chart yang ada sebelum membuat yang baru
        if (window.myDoughnutChart) {
            window.myDoughnutChart.destroy();
        }
        window.myDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Qty by Item',
                    data: values,
                    backgroundColor: [
                        '#f87171', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Set to false so the wrapper can control the size
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false
                    }
                }
            }
        });
    }

    function populateRecentTransactions(transactions) {
        const tbody = document.getElementById('recent-transactions-body');
        tbody.innerHTML = ''; // Clear loading text
        if (transactions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada transaksi.</td></tr>';
            return;
        }

        transactions.forEach(tx => {
            const row = document.createElement('tr');
            row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(tx.created_at).toLocaleString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${tx.item}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm ${tx.qty > 0 ? 'text-green-600' : 'text-red-600'} font-medium">${tx.qty}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tx.lokasi}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tx.group}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tx.user_nama}</td>
                `;
            tbody.appendChild(row);
        });
    }
</script>