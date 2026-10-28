<?php
// app/controller/master_data_lokasi_controller.php

session_name('lion_parcel_app');
session_start();

require_once('./config/config.php');
header('Content-Type: application/json');

// Pastikan user sudah login
if (!isset($_SESSION['user_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

// CREATE & UPDATE
if ($method === 'POST') {
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    if ($action === 'create') {
        // Cek duplikasi
        $check_sql = "SELECT id FROM lokasi WHERE lokasi = '$lokasi' AND deleted_at IS NULL";
        $check_res = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_res) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Lokasi sudah ada.']);
            exit();
        }

        $sql = "INSERT INTO lokasi (lokasi) VALUES ('$lokasi')";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Lokasi berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan lokasi.']);
        }
    } elseif ($action === 'update') {
        $sql = "UPDATE lokasi SET lokasi = '$lokasi' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Lokasi berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui lokasi.']);
        }
    }
}

// READ & DELETE
if ($method === 'GET') {
    if ($action === 'read') {
        $sql = "SELECT id, lokasi FROM lokasi WHERE deleted_at IS NULL ORDER BY lokasi ASC";
        $result = mysqli_query($conn, $sql);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } elseif ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        // Soft delete
        $sql = "UPDATE lokasi SET deleted_at = NOW() WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Lokasi berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus lokasi.']);
        }
    }
}

mysqli_close($conn);
