<?php
// logout.php

session_name('lion_parcel_app');
session_start();

header('Content-Type: application/json');

// Hapus semua variabel sesi
$_SESSION = array();

// Hancurkan sesi
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

echo json_encode(['status' => 'success', 'message' => 'Anda berhasil logout.']);
exit();
