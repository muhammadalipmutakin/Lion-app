<?php
// app/config/config.php
date_default_timezone_set('Asia/Jakarta');

// Kredensial Database
$db_host = 'sql200.infinityfree.com';
$db_user = 'if0_39709156'; // Ganti dengan username database Anda
$db_pass = '8fzqAIpcYpko';     // Ganti dengan password database Anda
$db_name = 'if0_39709156_lion_app'; // Ganti dengan nama database yang sudah Anda buat

// Melakukan koneksi ke database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
