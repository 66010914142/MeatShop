<?php
// 1. ปรับการดึง functions.php ให้แม่นยำขึ้นโดยใช้ dirname
$functions_path = dirname(__DIR__) . '/functions.php';

if (file_exists($functions_path)) {
    require_once $functions_path;
} else {
    // ป้องกัน Fatal Error หากหาไฟล์ไม่เจอ ให้สร้างฟังก์ชันสำรองไว้ชั่วคราว
    if (!function_exists('base_url')) {
        function base_url($path = '') {
            return '/MeatShop/' . $path;
        }
    }
    if (!function_exists('cart_count')) {
        function cart_count() { return 0; }
    }
}

// 2. จัดการ Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MSU Ni-sit Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="<?= base_url('css/Style.css') ?>" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Prompt', sans-serif; }
        .navbar-brand { color: #FFD700 !important; } /* บังคับสีเหลืองทอง มมส. */
        .nav-link:hover { color: #FFD700 !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-secondary shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="<?= base_url('front/index.php') ?>">
         🥩 MeatShop - สินค้า
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('track.php') ?>">ติดตามคำสั่งซื้อ</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-light position-relative" href="<?= base_url('cart.php') ?>">
                    <i class="fa-solid fa-cart-shopping"></i> ตะกร้า
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= cart_count() ?>
                    </a>

                <?php if (!empty($_SESSION['admin'])): ?>
                    <a class="btn btn-danger" href="<?= base_url('admin/index.php') ?>">แผงควบคุม</a>
                    <a class="btn btn-outline-light" href="<?= base_url('admin/logout.php') ?>">ออกจากระบบ</a>
                <?php elseif (!empty($_SESSION['user'])): ?>
                    <span class="text-white me-2">👋 <?= htmlspecialchars($_SESSION['user']['name'] ?? 'สมาชิก') ?></span>
                    <a class="btn btn-outline-light" href="<?= base_url('logout.php') ?>">ออกจากระบบ</a>
                <?php else: ?>
                    <a class="btn btn-outline-light" href="<?= ('front/login.php') ?>">เข้าสู่ระบบ</a>
                    <a class="btn btn-warning fw-bold" href="<?= ('front/register.php') ?>">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="py-4">
<div class="container">
