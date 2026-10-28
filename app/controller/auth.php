<?php
// app/controller/auth.php

// 1. Pindahkan semua konfigurasi sesi ke bagian paling atas
//    sebelum session_start().

// Atur umur session menjadi 1 hari (24 jam)
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

// Panggil session_name() dengan karakter yang valid
session_name('lion_parcel_app');
session_start();

// Perbaiki jalur require_once()
require_once(__DIR__ . '/./config/config.php');

// Mengatur header agar browser tahu bahwa respons adalah JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lindungi dari SQL Injection
    $username = mysqli_real_escape_string($conn, $username);

    $sql = "SELECT id, nama, password FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password yang di-hash
        if (password_verify($password, $user['password'])) {
            // Login berhasil

            // Simpan data ke variabel sesi dengan nama yang baru
            $_SESSION['user_data'] = [
                'id' => $user['id'],
                'nama' => $user['nama']
            ];

            echo json_encode(['status' => 'success']);
            exit();
        } else {
            // Password salah
            echo json_encode(['status' => 'error', 'message' => 'Password yang Anda masukkan salah.']);
            exit();
        }
    } else {
        // Username tidak ditemukan
        echo json_encode(['status' => 'error', 'message' => 'Username tidak terdaftar.']);
        exit();
    }
} else {
    // Jika akses langsung, berikan respons error
    echo json_encode(['status' => 'error', 'message' => 'Metode permintaan tidak valid.']);
    exit();
}

mysqli_close($conn);
