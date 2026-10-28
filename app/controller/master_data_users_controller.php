<?php
// app/controller/master_data_users_controller.php

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
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'] ?? '';
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    if ($action === 'create') {
        // Cek duplikasi username
        $check_sql = "SELECT id FROM users WHERE username = '$username' AND deleted_at IS NULL";
        $check_res = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_res) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah terdaftar.']);
            exit();
        }

        // Hash password dari input
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$hashed_password')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'User berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan user.']);
        }
    } elseif ($action === 'update') {
        $sql = "UPDATE users SET nama = '$nama', username = '$username'";

        // Jika password diisi, hash dan tambahkan ke query UPDATE
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = '$hashed_password'";
        }

        $sql .= " WHERE id = '$id'";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'User berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui user.']);
        }
    }
}

// READ & DELETE
if ($method === 'GET') {
    if ($action === 'read') {
        $sql = "SELECT id, nama, username FROM users WHERE deleted_at IS NULL ORDER BY nama ASC";
        $result = mysqli_query($conn, $sql);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } elseif ($action === 'delete') {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        // Soft delete
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'User berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus user.']);
        }
    }
}

mysqli_close($conn);
