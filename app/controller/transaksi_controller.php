<?php
session_name('lion_parcel_app');
session_start();

require_once('./config/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit();
}

$user_data = $_SESSION['user_data'];

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'get_dropdown_data') {
        $response = [];
        $sql_group = "SELECT id, `group` FROM `group` WHERE deleted_at IS NULL";
        $result_group = mysqli_query($conn, $sql_group);
        $response['group'] = mysqli_fetch_all($result_group, MYSQLI_ASSOC);

        $sql_lokasi = "SELECT id, lokasi FROM lokasi WHERE deleted_at IS NULL";
        $result_lokasi = mysqli_query($conn, $sql_lokasi);
        $response['lokasi'] = mysqli_fetch_all($result_lokasi, MYSQLI_ASSOC);

        $response['status'] = 'success';
        echo json_encode($response);
        exit();
    }

    if (isset($_GET['action']) && $_GET['action'] === 'search_by_resi') {
        $stt_resi_to_search = mysqli_real_escape_string($conn, $_GET['resi'] ?? '');

        if (empty($stt_resi_to_search)) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor resi tidak boleh kosong.']);
            exit();
        }

        $sql_search_resi = "
            SELECT 
                t.stt_resi, 
                t.ean, 
                t.imei, 
                t.sn, 
                t.item, 
                SUM(t.qty) as qty, 
                t.id_lokasi, 
                t.id_group, 
                l.lokasi, 
                g.group
            FROM transaksi AS t
            LEFT JOIN lokasi AS l ON t.id_lokasi = l.id
            LEFT JOIN `group` AS g ON t.id_group = g.id
            WHERE t.stt_resi = '{$stt_resi_to_search}' AND t.deleted_at IS NULL
            GROUP BY t.stt_resi, t.ean, t.imei, t.sn, t.item, t.id_lokasi, t.id_group
            HAVING SUM(t.qty) > 0
            LIMIT 1
        ";

        $result_search_resi = mysqli_query($conn, $sql_search_resi);
        $data_found = mysqli_fetch_assoc($result_search_resi);

        if ($data_found) {
            echo json_encode(['status' => 'success', 'data' => $data_found]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
        exit();
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_transaksi = $_POST['jenis'] ?? '';
    $stt_resi = mysqli_real_escape_string($conn, $_POST['stt_resi'] ?? '');
    $ean = mysqli_real_escape_string($conn, $_POST['ean'] ?? '');
    $imei = mysqli_real_escape_string($conn, $_POST['imei'] ?? '');
    $sn = mysqli_real_escape_string($conn, $_POST['sn'] ?? '');
    $item = mysqli_real_escape_string($conn, $_POST['item'] ?? '');
    $qty = (float)($_POST['qty'] ?? 0);
    $id_group = (int)($_POST['id_group'] ?? 0);
    $id_lokasi = (int)($_POST['id_lokasi'] ?? 0);
    $id_user = $user_data['id'];

    // Tangkap data timestamp dari frontend
    $timestamp = mysqli_real_escape_string($conn, $_POST['timestamp'] ?? '');

    if (empty($item) || $qty <= 0 || $id_group <= 0 || $id_lokasi <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data input tidak lengkap atau tidak valid.']);
        exit();
    }

    if ($jenis_transaksi === 'outbound') {
        $sql_cek_stok = "SELECT SUM(qty) as total_stok FROM transaksi WHERE item = '{$item}' AND id_lokasi = {$id_lokasi} AND id_group = {$id_group} AND deleted_at IS NULL";
        $result_cek_stok = mysqli_query($conn, $sql_cek_stok);
        $data_stok = mysqli_fetch_assoc($result_cek_stok);
        $total_stok = $data_stok['total_stok'] ?? 0;

        if ($total_stok < $qty) {
            echo json_encode(['status' => 'error', 'message' => "Stok item '{$item}' pada lokasi/group ini tidak mencukupi. Tersisa: {$total_stok}."]);
            exit();
        }
        $qty = -$qty;
    }

    // Ubah query INSERT untuk memasukkan nilai created_at dari frontend
    $sql = "INSERT INTO transaksi (stt_resi, ean, imei, sn, item, qty, id_group, id_lokasi, id_user, created_at) 
            VALUES ('{$stt_resi}', '{$ean}', '{$imei}', '{$sn}', '{$item}', {$qty}, {$id_group}, {$id_lokasi}, {$id_user}, '{$timestamp}')";

    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);
        $sql_get_data = "
            SELECT 
                t.stt_resi, 
                t.ean, 
                t.imei, 
                t.sn, 
                t.item, 
                t.qty, 
                l.lokasi as lokasi, 
                g.group as `group`, 
                u.nama as `user`
            FROM transaksi AS t
            LEFT JOIN lokasi AS l ON t.id_lokasi = l.id
            LEFT JOIN `group` AS g ON t.id_group = g.id
            LEFT JOIN users AS u ON t.id_user = u.id
            WHERE t.id = {$last_id}
        ";
        $result_data = mysqli_query($conn, $sql_get_data);
        $transaction_data = mysqli_fetch_assoc($result_data);
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan.', 'data' => $transaction_data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi: ' . mysqli_error($conn)]);
    }
    exit();
}

mysqli_close($conn);

echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
exit();
