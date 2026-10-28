<?php
// app/controller/movement_controller.php

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
    $sql_item = "SELECT DISTINCT item FROM transaksi WHERE deleted_at IS NULL";
    $result_item = mysqli_query($conn, $sql_item);
    $response['item'] = mysqli_fetch_all($result_item, MYSQLI_ASSOC);

    // Ambil data 'group'
    $sql_group = "SELECT id, `group` FROM `group` WHERE deleted_at IS NULL";
    $result_group = mysqli_query($conn, $sql_group);
    $response['group'] = mysqli_fetch_all($result_group, MYSQLI_ASSOC);

    // Ambil data 'lokasi'
    $sql_lokasi = "SELECT id, lokasi FROM lokasi WHERE deleted_at IS NULL";
    $result_lokasi = mysqli_query($conn, $sql_lokasi);
    $response['lokasi'] = mysqli_fetch_all($result_lokasi, MYSQLI_ASSOC);

    $response['status'] = 'success';
    echo json_encode($response);
    exit();
}

// Fungsi untuk mendapatkan data movement
if ($action === 'get_movement_data') {
    $startDate = mysqli_real_escape_string($conn, $_GET['start_date']);
    $endDate = mysqli_real_escape_string($conn, $_GET['end_date']);
    $filterItems = $_GET['items'] ?? [];
    $filterLokasis = $_GET['lokasis'] ?? [];
    $filterGroups = $_GET['groups'] ?? [];
    $filterType = mysqli_real_escape_string($conn, $_GET['type'] ?? '');

    $sql = "
        SELECT
            t.*,
            l.lokasi,
            g.group,
            u.nama AS nama_user
        FROM transaksi AS t
        LEFT JOIN lokasi AS l ON t.id_lokasi = l.id
        LEFT JOIN `group` AS g ON t.id_group = g.id
        LEFT JOIN users AS u ON t.id_user = u.id
        WHERE t.deleted_at IS NULL
        AND t.created_at >= '$startDate 00:00:00'
        AND t.created_at <= '$endDate 23:59:59'
    ";

    $whereClauses = [];
    if (!empty($filterItems)) {
        $quotedItems = array_map(function ($item) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $item) . "'";
        }, $filterItems);
        $whereClauses[] = "t.item IN (" . implode(", ", $quotedItems) . ")";
    }
    if (!empty($filterLokasis)) {
        $quotedLokasis = array_map(function ($lokasi) use ($conn) {
            return (int) $lokasi;
        }, $filterLokasis);
        $whereClauses[] = "t.id_lokasi IN (" . implode(", ", $quotedLokasis) . ")";
    }
    if (!empty($filterGroups)) {
        $quotedGroups = array_map(function ($group) use ($conn) {
            return (int) $group;
        }, $filterGroups);
        $whereClauses[] = "t.id_group IN (" . implode(", ", $quotedGroups) . ")";
    }
    if (!empty($filterType)) {
        if ($filterType === 'In') {
            $whereClauses[] = "t.qty > 0";
        } else if ($filterType === 'Out') {
            $whereClauses[] = "t.qty < 0";
        }
    }

    // Tambahkan klausa WHERE jika ada filter tambahan
    if (!empty($whereClauses)) {
        $sql .= " AND " . implode(" AND ", $whereClauses);
    }

    $sql .= " ORDER BY t.created_at DESC";

    $result = mysqli_query($conn, $sql);
    $movementData = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode(['status' => 'success', 'movements' => $movementData]);
    exit();
}

// FUNGSI BARU: Untuk memperbarui data movement
if ($action === 'update_movement' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = (int)($data['id'] ?? 0);
    $id_lokasi = (int)($data['id_lokasi'] ?? 0);
    $id_group = (int)($data['id_group'] ?? 0);
    $qty = (int)($data['qty'] ?? 0);
    $stt_resi = mysqli_real_escape_string($conn, $data['stt_resi'] ?? '');
    $ean = mysqli_real_escape_string($conn, $data['ean'] ?? '');
    $imei = mysqli_real_escape_string($conn, $data['imei'] ?? '');

    if ($id > 0) {
        $sql = "UPDATE transaksi SET 
                    id_lokasi = $id_lokasi, 
                    id_group = $id_group, 
                    qty = $qty, 
                    stt_resi = '$stt_resi', 
                    ean = '$ean', 
                    imei = '$imei' 
                WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID transaksi tidak valid.']);
    }
    exit();
}

// FUNGSI BARU: Untuk menghapus data movement (soft delete)
if ($action === 'delete_movement' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_GET['id'] ?? 0);

    if ($id > 0) {
        // Soft delete dengan menandai deleted_at
        $sql = "UPDATE transaksi SET deleted_at = NOW() WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID transaksi tidak valid.']);
    }
    exit();
}


mysqli_close($conn);

echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
