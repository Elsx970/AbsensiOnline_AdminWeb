<?php
session_start();

// Buat base URL yang dinamis agar tidak rusak saat pindah VPS/Folder
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$uri_parts = explode('/', $_SERVER['REQUEST_URI']);
$admin_path = "";
foreach($uri_parts as $part) {
    $admin_path .= $part . "/";
    if($part == 'admin') break;
}
$base_admin_url = $base_url . rtrim($admin_path, '/');

if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: " . $base_admin_url . "/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #003366; }
        .sidebar a { color: rgba(255,255,255,.8); text-decoration: none; padding: 12px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,.1); color: #fff; border-left: 4px solid #f39c12; }
    </style>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar text-white" style="width: 250px;">
            <div class="p-3 mb-3 text-center border-bottom border-secondary">
                <h5 class="fw-bold m-0">ADMIN PRESENSI</h5>
            </div>
            <a href="<?= $base_admin_url ?>/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="<?= $base_admin_url ?>/users/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'users' ? 'active' : '' ?>"><i class="bi bi-people me-2"></i> Data Mahasiswa</a>
            <a href="<?= $base_admin_url ?>/lokasi/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'lokasi' ? 'active' : '' ?>"><i class="bi bi-journal-bookmark me-2"></i> Data Mata Kuliah & Jadwal</a>
            <a href="<?= $base_admin_url ?>/absensi/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'absensi' ? 'active' : '' ?>"><i class="bi bi-list-check me-2"></i> Data Presensi</a>

            <a href="<?= $base_admin_url ?>/logout.php" class="mt-5 text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>
        
        <!-- Content -->
        <div class="flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3">
                <span class="navbar-brand mb-0 h1">Panel Administrator</span>
                <div class="ms-auto">
                    <span class="me-3">Halo, <?= $_SESSION['admin_nama'] ?></span>
                </div>
            </nav>
            <div class="p-4">
