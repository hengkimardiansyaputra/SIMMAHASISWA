<?php
// includes/auth.php
// Sertakan file ini di SEMUA halaman yang membutuhkan login
// Letakkan SEBELUM require header.php

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['flash'] = [
        'type'    => 'warning',
        'message' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.'
    ];
    header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/login.php');
    exit;
}
