<?php
// app/controller/stock_controller.php

session_name('lion_parcel_app');
session_start();

require_once('./config/config.php');
header('Content-Type: application/json');

// Pastikan user sudah login
if (!isset($_SESSION['user_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit();
}

$action = $_GET['action'] ?? '';

// Fungsi untuk mendapatkan data dropdown
if ($action === 'get_dropdown_data') {
    $response = [];

    // Ambil semua item unik
    $sql_item = "SELECT DISTINCT item FROM transaksi WHERE deleted_at IS NULL ORDER BY item ASC";
    $result_item = mysqli_query($conn, $sql_item);
    $response['item'] = mysqli_fetch_all($result_item, MYSQLI_ASSOC);

    // Ambil data 'group'
    $sql_group = "SELECT id, `group` FROM `group` WHERE deleted_at IS NULL ORDER BY `group` ASC";
    $result_group = mysqli_query($conn, $sql_group);
    $response['group'] = mysqli_fetch_all($result_group, MYSQLI_ASSOC);

    // Ambil data 'lokasi'
    $sql_lokasi = "SELECT id, lokasi FROM lokasi WHERE deleted_at IS NULL ORDER BY lokasi ASC";
    $result_lokasi = mysqli_query($conn, $sql_lokasi);
    $response['lokasi'] = mysqli_fetch_all($result_lokasi, MYSQLI_ASSOC);

    $response['status'] = 'success';
    echo json_encode($response);
    exit();
}

// Fungsi untuk mendapatkan data stok ringkas
if ($action === 'get_stock_data') {
    $startDate = mysqli_real_escape_string($conn, $_GET['start_date']);
    $endDate = mysqli_real_escape_string($conn, $_GET['end_date']);

    // Ambil nilai filter sebagai string yang dipisahkan koma
    $filterItems = $_GET['item'] ?? '';
    $filterLokasi = $_GET['lokasi'] ?? '';
    $filterGroup = $_GET['group'] ?? '';

    $sql = "
        SELECT
            t.item,
            SUM(CASE WHEN t.created_at < '$startDate 00:00:00' THEN t.qty ELSE 0 END) AS stok_awal,
            SUM(CASE WHEN t.created_at >= '$startDate 00:00:00' AND t.created_at <= '$endDate 23:59:59' AND t.qty > 0 THEN t.qty ELSE 0 END) AS masuk,
            SUM(CASE WHEN t.created_at >= '$startDate 00:00:00' AND t.created_at <= '$endDate 23:59:59' AND t.qty < 0 THEN t.qty ELSE 0 END) AS keluar,
            SUM(t.qty) AS stok_akhir,
            t.id_lokasi,
            t.id_group
        FROM transaksi AS t
        WHERE t.deleted_at IS NULL
    ";

    $whereClauses = [];

    // Logika untuk filter multi-select Item
    if (!empty($filterItems)) {
        $itemsArray = explode(',', $filterItems);
        $escapedItems = array_map(function ($item) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $item) . "'";
        }, $itemsArray);
        $whereClauses[] = "t.item IN (" . implode(",", $escapedItems) . ")";
    }

    // Logika untuk filter multi-select Lokasi
    if (!empty($filterLokasi)) {
        $lokasiArray = explode(',', $filterLokasi);
        // Untuk integer, tidak perlu tanda kutip
        $escapedLokasi = array_map(function ($lokasi) use ($conn) {
            return mysqli_real_escape_string($conn, $lokasi);
        }, $lokasiArray);
        $whereClauses[] = "t.id_lokasi IN (" . implode(",", $escapedLokasi) . ")";
    }

    // Logika untuk filter multi-select Grouping
    if (!empty($filterGroup)) {
        $groupArray = explode(',', $filterGroup);
        // Untuk integer, tidak perlu tanda kutip
        $escapedGroup = array_map(function ($group) use ($conn) {
            return mysqli_real_escape_string($conn, $group);
        }, $groupArray);
        $whereClauses[] = "t.id_group IN (" . implode(",", $escapedGroup) . ")";
    }

    if (!empty($whereClauses)) {
        $sql .= " AND " . implode(" AND ", $whereClauses);
    }

    $sql .= " GROUP BY t.item HAVING SUM(t.qty) != 0 ORDER BY t.item ASC";

    $result = mysqli_query($conn, $sql);
    $stockData = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $formattedData = array_map(function ($row) {
        $row['masuk'] = (int) $row['masuk'];
        $row['keluar'] = (int) abs($row['keluar']);
        $row['stok_awal'] = (int) $row['stok_awal'];
        $row['stok_akhir'] = (int) $row['stok_akhir'];
        return $row;
    }, $stockData);

    echo json_encode(['status' => 'success', 'stock' => $formattedData]);
    exit();
}

// Fungsi untuk mendapatkan detail transaksi
if ($action === 'get_transaction_details') {
    $item = mysqli_real_escape_string($conn, $_GET['item']);
    $startDate = mysqli_real_escape_string($conn, $_GET['start_date']);
    $endDate = mysqli_real_escape_string($conn, $_GET['end_date']);

    $sql = "
        SELECT
            t.created_at,
            t.stt_resi,
            t.ean,
            t.imei,
            t.sn,
            t.qty,
            u.nama AS nama_user,
            l.lokasi,
            g.group,
            CASE
                WHEN t.qty > 0 THEN 'Masuk'
                ELSE 'Keluar'
            END AS jenis
        FROM transaksi AS t
        LEFT JOIN users AS u ON t.id_user = u.id
        LEFT JOIN lokasi AS l ON t.id_lokasi = l.id
        LEFT JOIN `group` AS g ON t.id_group = g.id
        WHERE t.item = '$item'
        AND t.created_at >= '$startDate 00:00:00'
        AND t.created_at <= '$endDate 23:59:59'
        AND t.deleted_at IS NULL
        ORDER BY t.created_at DESC
    ";

    $result = mysqli_query($conn, $sql);
    $transactionDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode(['status' => 'success', 'transactions' => $transactionDetails]);
    exit();
}

// Fungsi baru untuk mendapatkan detail stok
if ($action === 'get_stock_details') {
    $item = mysqli_real_escape_string($conn, $_GET['item']);

    $sql = "
        SELECT
            l.lokasi,
            g.group,
            SUM(t.qty) AS stok_akhir
        FROM transaksi AS t
        LEFT JOIN lokasi AS l ON t.id_lokasi = l.id
        LEFT JOIN `group` AS g ON t.id_group = g.id
        WHERE t.item = '$item'
        AND t.deleted_at IS NULL
        GROUP BY l.lokasi, g.group
        HAVING SUM(t.qty) != 0
        ORDER BY l.lokasi ASC, g.group ASC
    ";

    $result = mysqli_query($conn, $sql);
    $stockDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode(['status' => 'success', 'details' => $stockDetails]);
    exit();
}

mysqli_close($conn);

echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
