<?php
// app/controller/master_data_group_controller.php

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
    $group = mysqli_real_escape_string($conn, $_POST['group']);
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    if ($action === 'create') {
        // Cek duplikasi
        $check_sql = "SELECT id FROM `group` WHERE `group` = '$group' AND deleted_at IS NULL";
        $check_res = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_res) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Group sudah ada.']);
            exit();
        }

        $sql = "INSERT INTO `group` (`group`) VALUES ('$group')";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Group berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan group.']);
        }
    } elseif ($action === 'update') {
        $sql = "UPDATE `group` SET `group` = '$group' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Group berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui group.']);
        }
    }
}

// READ & DELETE
if ($method === 'GET') {
    if ($action === 'read') {
        $sql = "SELECT id, `group` FROM `group` WHERE deleted_at IS NULL ORDER BY `group` ASC";
        $result = mysqli_query($conn, $sql);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } elseif ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        // Soft delete
        $sql = "UPDATE `group` SET deleted_at = NOW() WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Group berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus group.']);
        }
    }
}

mysqli_close($conn);
