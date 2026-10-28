<?php
// app/controller/user_controller.php

session_name('lion_parcel_app');
session_start();

require_once('./config/config.php');
header('Content-Type: application/json');

// Pastikan user sudah login
if (!isset($_SESSION['user_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit();
}

$action = $_REQUEST['action'] ?? '';

// Ambil data user yang sedang login
if ($action === 'get_user_data') {
    $user_id = $_SESSION['user_data']['id'];
    $sql = "SELECT id, nama, username FROM users WHERE id = '$user_id' AND deleted_at IS NULL";
    $result = mysqli_query($conn, $sql);
    $user_data = mysqli_fetch_assoc($result);

    if ($user_data) {
        echo json_encode(['status' => 'success', 'data' => $user_data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data user tidak ditemukan.']);
    }
    exit();
}

// Ubah Password
if ($action === 'change_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_data']['id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    // Ambil password user dari database
    $sql_get_pass = "SELECT password FROM users WHERE id = '$user_id'";
    $result_get_pass = mysqli_query($conn, $sql_get_pass);
    $user_pass_db = mysqli_fetch_assoc($result_get_pass);

    // Verifikasi password saat ini
    if (!password_verify($current_password, $user_pass_db['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Password saat ini salah.']);
        exit();
    }

    // Hash password baru
    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql_update_pass = "UPDATE users SET password = '$hashed_new_password' WHERE id = '$user_id'";

    if (mysqli_query($conn, $sql_update_pass)) {
        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diubah.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah password.']);
    }
    exit();
}

mysqli_close($conn);

echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
