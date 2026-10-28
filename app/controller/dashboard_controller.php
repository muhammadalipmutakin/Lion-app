<?php
// dashboard_api.php

session_name('lion_parcel_app');
session_start();

require_once('./config/config.php');
header('Content-Type: application/json');

// Pastikan user sudah login
if (!isset($_SESSION['user_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit();
}

$response = [
    'status' => 'success',
    'message' => 'Data dashboard berhasil dimuat.',
    'data' => []
];

// Query untuk mendapatkan total kuantitas (stok) barang yang ada
$sql_total_qty = "SELECT SUM(qty) AS total_qty FROM transaksi WHERE deleted_at IS NULL";
$result_total_qty = mysqli_query($conn, $sql_total_qty);
$total_qty = mysqli_fetch_assoc($result_total_qty)['total_qty'] ?? 0;
$response['data']['total_qty'] = (int) $total_qty;

// Query untuk mendapatkan total transaksi masuk (qty > 0)
$sql_total_in = "SELECT COUNT(*) AS total_in FROM transaksi WHERE qty > 0 AND deleted_at IS NULL";
$result_total_in = mysqli_query($conn, $sql_total_in);
$total_in = mysqli_fetch_assoc($result_total_in)['total_in'] ?? 0;
$response['data']['total_in'] = (int) $total_in;

// Query untuk mendapatkan total transaksi keluar (qty < 0)
$sql_total_out = "SELECT COUNT(*) AS total_out FROM transaksi WHERE qty < 0 AND deleted_at IS NULL";
$result_total_out = mysqli_query($conn, $sql_total_out);
$total_out = mysqli_fetch_assoc($result_total_out)['total_out'] ?? 0;
$response['data']['total_out'] = (int) $total_out;

// Query untuk data chart: total qty per lokasi
$sql_qty_by_lokasi = "
    SELECT
        l.lokasi,
        SUM(t.qty) AS total_qty_lokasi
    FROM transaksi AS t
    LEFT JOIN lokasi AS l ON t.id_lokasi = l.id
    WHERE t.deleted_at IS NULL
    GROUP BY t.id_lokasi
    ORDER BY total_qty_lokasi DESC
";
$result_qty_by_lokasi = mysqli_query($conn, $sql_qty_by_lokasi);
$response['data']['qty_by_lokasi'] = mysqli_fetch_all($result_qty_by_lokasi, MYSQLI_ASSOC);

// Query untuk data chart: total qty per item (top 5)
$sql_qty_by_item = "
    SELECT
        t.item,
        SUM(t.qty) AS total_qty_item
    FROM transaksi AS t
    WHERE t.deleted_at IS NULL
    GROUP BY t.item
    ORDER BY total_qty_item DESC
    LIMIT 5
";
$result_qty_by_item = mysqli_query($conn, $sql_qty_by_item);
$response['data']['qty_by_item'] = mysqli_fetch_all($result_qty_by_item, MYSQLI_ASSOC);

// Query untuk mendapatkan 10 transaksi terbaru
$sql_recent_transactions = "
    SELECT
        t.created_at,
        t.item,
        t.qty,
        l.lokasi,
        g.group,
        u.nama AS user_nama
    FROM transaksi AS t
    LEFT JOIN lokasi AS l ON t.id_lokasi = l.id
    LEFT JOIN `group` AS g ON t.id_group = g.id
    LEFT JOIN users AS u ON t.id_user = u.id
    WHERE t.deleted_at IS NULL
    ORDER BY t.created_at DESC
    LIMIT 10
";
$result_recent_transactions = mysqli_query($conn, $sql_recent_transactions);
$response['data']['recent_transactions'] = mysqli_fetch_all($result_recent_transactions, MYSQLI_ASSOC);

echo json_encode($response);
mysqli_close($conn);
