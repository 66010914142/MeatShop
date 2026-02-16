<?php
// 1. จัดการเรื่อง Session และ Path การดึงข้อมูล
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ปรับการดึง functions.php ให้แม่นยำ (อ้างอิงตามโครงสร้างใน VS Code)
$functions_path = __DIR__ . '/functions.php';

if (file_exists($functions_path)) {
    require_once $functions_path;
} else {
    // ฟังก์ชันสำรองกรณีหาไฟล์ functions.php ไม่เจอ
    if (!function_exists('base_url')) {
        function base_url($path = '') {
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
        /* MSU Premium Branding Style */
        body { 
            font-family: 'Prompt', sans-serif; 
            background-color: #f4f4f4; 
        }
        
        .navbar-msu {
            background-color: #2c3e50 !important; /* สีเทาเข้ม Slate */
            border-bottom: 4px solid #FFD700; /* เส้นใต้สีทอง มมส. */
        }
        
        .navbar-brand { 
            color: #FFD700 !important; 
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            letter-spacing: 1px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            transition: 0.3s;
        }
        
        .nav-link:hover {
            color: #FFD700 !important;
        }

        .btn-msu-gold {
            background-color: #FFD700;
            color: #2c3e50;
            font-weight: 700;
            border: none;
            transition: 0.3s;
        }

        .btn-msu-gold:hover {
            background-color: #e6c200;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255,215,0,0.3);
        }

        .cart-badge {
            font-size: 0.65rem;
            background-color: #e74c3c !important; /* สีแดงแจ้งเตือนให้ตัดกับสีทอง */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-msu shadow-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="<?= base_url('index.php') ?>">
           <i class="fa-solid fa-drumstick-bite me-2"></i>MeatShop
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link px-3" href="<?= base_url('index.php') ?>">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="<?= base_url('track.php') ?>">ติดตามคำสั่งซื้อ</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a class="btn btn-outline-light position-relative btn-sm border-0" href="<?= base_url('cart.php') ?>">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge">
                        <?= cart_count() ?>
                    </span>
                </a>

                <?php 
                // ตรวจสอบการ Login จากฐานข้อมูล user_login
                if (isset($_SESSION['user_id'])): 
                ?>
                    <div class="dropdown">
                        <button class="btn btn-link text-white text-decoration-none dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user fs-4 me-1"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item py-2" href="<?= base_url('profile.php') ?>"><i class="fa-solid fa-id-card me-2"></i>ข้อมูลส่วนตัว</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger py-2" href="<?= base_url('logout.php') ?>"><i class="fa-solid fa-power-off me-2"></i>ออกจากระบบ</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-2">
                        <a class="btn btn-link text-white text-decoration-none btn-sm" href="<?= base_url('login.php') ?>">เข้าสู่ระบบ</a>
                        <a class="btn btn-msu-gold btn-sm px-4 rounded-pill shadow-sm" href="<?= base_url('register.php') ?>">สมัครสมาชิก</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="py-5">
<div class="container bg-white p-4 rounded-4 shadow-sm min-vh-100">