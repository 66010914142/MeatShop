<?php
// 1. จัดการเรื่อง Session และ Path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ปรับการดึง functions.php (อ้างอิงตามโครงสร้างใน VS Code ของคุณ)
$functions_path = __DIR__ . '/functions.php';

if (file_exists($functions_path)) {
    require_once $functions_path;
} else {
    // ฟังก์ชันสำรองกรณีหาไฟล์ functions.php ไม่เจอ
    if (!function_exists('base_url')) {
        function base_url($path = '') {
            // ปรับให้รองรับทั้ง localhost และ production
            return '/MeatShop/' . ltrim($path, '/');
        }
    }
    if (!function_exists('cart_count')) {
        function cart_count() { 
            return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MeatShop - MSU Ni-sit Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="<?= base_url('css/Style.css') ?>" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f8f9fa; }
        .navbar-brand { color: #FFD700 !important; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); } /* สีทอง มมส. */
        .bg-msu { background-color: #6c757d !important; } /* สีเทา Secondary */
        .nav-link:hover { color: #FFD700 !important; }
        .cart-badge { font-size: 0.65rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-msu shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="<?= base_url('index.php') ?>">
           <i class="fa-solid fa-drumstick-bite"></i> MeatShop
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php') ?>">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('track.php') ?>">ติดตามคำสั่งซื้อ</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a class="btn btn-light position-relative btn-sm" href="<?= base_url('cart.php') ?>">
                    <i class="fa-solid fa-cart-shopping"></i> ตะกร้า
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                        <?= cart_count() ?>
                    </span>
                </a>

                <?php 
                // ปรับเงื่อนไขการเช็ค Session ให้ตรงกับที่เก็บใน login.php
                if (isset($_SESSION['user_id'])): 
                ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            👋 <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('profile.php') ?>"><i class="fa-solid fa-user me-2"></i>ข้อมูลส่วนตัว</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('logout.php') ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>ออกจากระบบ</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="btn-group btn-group-sm">
                        <a class="btn btn-outline-light px-3" href="<?= base_url('login.php') ?>">เข้าสู่ระบบ</a>
                        <a class="btn btn-warning fw-bold px-3" href="<?= base_url('register.php') ?>">สมัครสมาชิก</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="py-4">
<div class="container">