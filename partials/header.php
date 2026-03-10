<?php
// 1. จัดการเรื่อง Session และ Database Connection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('config/connectdb.php');

// ฟังก์ชันพื้นฐาน
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

// 2. เตรียมข้อมูล
$cat_id = isset($_GET['C_id']) ? mysqli_real_escape_string($conn, $_GET['C_id']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql_cat = "SELECT * FROM categories ORDER BY C_id ASC";
$query_cat = mysqli_query($conn, $sql_cat);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MeatShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Prompt', sans-serif; background-color: #f8f9fa; }
        .navbar-msu { background-color: #2c3e50 !important; border-bottom: 4px solid #FFD700; }
        .navbar-brand { color: #FFD700 !important; font-weight: 700; }
        .nav-link { color: rgba(255,255,255,0.8) !important; transition: 0.3s; }
        .nav-link:hover { color: #FFD700 !important; }
        .btn-msu-gold { background-color: #FFD700; color: #2c3e50; font-weight: 700; border: none; }
        .cart-badge { font-size: 0.65rem; background-color: #e74c3c !important; }
        .search-container { max-width: 400px; width: 100%; }
        .dropdown-menu { border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-msu shadow-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fs-3" href="<?= base_url('index.php') ?>">
           <i class="fa-solid fa-drumstick-bite me-2"></i>MeatShop
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="catDrop" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        หมวดหมู่สินค้า
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="catDrop">
                        <li><a class="dropdown-item" href="index.php">ทั้งหมด</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php while($c = mysqli_fetch_assoc($query_cat)): ?>
                            <li>
                                <a class="dropdown-item <?= ($cat_id == $c['C_id']) ? 'active' : '' ?>" 
                                   href="index.php?C_id=<?= $c['C_id'] ?>">
                                    <?= htmlspecialchars($c['c_name_th']) ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </li>
            </ul>

            <form action="index.php" method="GET" class="d-flex mx-lg-auto my-2 my-lg-0 search-container">
                <div class="input-group">
                    <input class="form-control border-end-0" type="search" name="search" 
                           placeholder="ค้นหาสินค้า..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-light border-start-0 text-primary" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <a class="btn btn-outline-light position-relative btn-sm border-0" href="<?= base_url('cart.php') ?>">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge">
                        <?= cart_count() ?>
                    </span>
                </a>

                <?php if (isset($_SESSION['u_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-link text-white text-decoration-none dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user fs-4 me-1 text-warning"></i> <?= htmlspecialchars($_SESSION['u_name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item py-2" href="<?= base_url('profile.php') ?>">ข้อมูลส่วนตัว</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger py-2" href="javascript:void(0)" onclick="confirmLogout()">ออกจากระบบ</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-2">
                        <a class="btn btn-link text-white text-decoration-none btn-sm" href="<?= base_url('login.php') ?>">เข้าสู่ระบบ</a>
                        <a class="btn btn-msu-gold btn-sm px-3 rounded-pill" href="<?= base_url('register.php') ?>">สมัครสมาชิก</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmLogout() {
    Swal.fire({
        title: 'ยืนยันออกจากระบบ?',
        text: "คุณต้องการออกจากระบบ MeatShop ใช่หรือไม่",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2c3e50',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ออกจากระบบ',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url("logout.php") ?>';
        }
    })
}
</script>

</body>
</html>
