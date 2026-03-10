<?php
    // 1. ตรวจสอบการ Login และเชื่อมต่อ DB
    include_once("check_login.php");
    include_once("connectdb.php"); 

    // เช็คชื่อไฟล์ปัจจุบันเพื่อทำแถบ Active ใน Sidebar
    $current_page = basename($_SERVER['PHP_SELF']);

    // 2. ดึงข้อมูลรายงานจากฐานข้อมูลจริง
    
    // นับสินค้าทั้งหมด
    $sql_p = "SELECT COUNT(P_id) as total FROM products";
    $res_p = mysqli_query($conn, $sql_p);
    $count_prod = mysqli_fetch_array($res_p)['total'] ?? 0;

    // นับออเดอร์รอชำระเงิน
    $sql_o = "SELECT COUNT(or_id) as total FROM orders WHERE or_status = 'รอชำระเงิน'";
    $res_o = mysqli_query($conn, $sql_o);
    $count_order = mysqli_fetch_array($res_o)['total'] ?? 0;

    // คำนวณยอดขายรวมจากออเดอร์ที่จัดส่งแล้ว
    $sql_s = "SELECT SUM(or_total_amount) as total FROM orders WHERE or_status = 'จัดส่งแล้ว'";
    $res_s = mysqli_query($conn, $sql_s);
    $total_sales = mysqli_fetch_array($res_s)['total'] ?? 0;
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - MEAT SHOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link:hover { background-color: #343a40; color: #fff; padding-left: 25px; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { padding: 25px; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-5px); }
        .navbar-custom { background: white; border-radius: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        
        /* DataTables Custom Style */
        .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
        .dataTables_filter input { border-radius: 20px; padding: 5px 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-0 shadow">
            <div class="p-4 text-center fw-bold border-bottom border-secondary">
                <i class="fa-solid fa-store me-2 text-info"></i> MEAT SHOP
            </div>
            <div class="nav flex-column nav-pills mt-2">
                <a href="index2.php" class="nav-link <?php echo ($current_page == 'index2.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-home me-2"></i> หน้าหลักแอดมิน
                </a>
                <a href="categories.php" class="nav-link <?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-layer-group me-2"></i> จัดการประเภทสินค้า
                </a>
                <a href="products.php" class="nav-link <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box me-2"></i> จัดการสินค้า
                </a>
                <a href="orders.php" class="nav-link <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์
                </a>
                <a href="customers.php" class="nav-link <?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users me-2"></i> จัดการลูกค้า
                </a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
                </a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            
            <div class="navbar-custom d-flex justify-content-between align-items-center p-3 mb-4">
                <h4 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-chart-line me-2"></i> Dashboard Overview</h4>
                <div class="d-flex align-items-center">
                    <span class="me-3 d-none d-md-inline text-muted small">ผู้ดูแลระบบ: <strong><?php echo $_SESSION['a.name'] ?? 'Admin'; ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D6EFD&color=fff" class="rounded-circle shadow-sm" width="35">
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card bg-primary text-white h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-uppercase opacity-75 small fw-bold">สินค้าทั้งหมด</h6>
                                    <h2 class="fw-bold mb-0"><?php echo number_format($count_prod); ?></h2>
                                </div>
                                <i class="fa-solid fa-box fa-2x opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-success text-white h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-uppercase opacity-75 small fw-bold">ยอดขายรวมสำเร็จ</h6>
                                    <h2 class="fw-bold mb-0">฿<?php echo number_format($total_sales, 2); ?></h2>
                                </div>
                                <i class="fa-solid fa-hand-holding-dollar fa-2x opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-warning text-dark h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-uppercase opacity-75 small fw-bold">ออเดอร์รอจัดการ</h6>
                                    <h2 class="fw-bold mb-0"><?php echo number_format($count_order); ?></h2>
                                </div>
                                <i class="fa-solid fa-clock-rotate-left fa-2x opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="row g-0">
                    <div class="col-md-8 p-5">
                        <h3 class="fw-bold text-dark">ยินดีต้อนรับกลับมา! 👋</h3>
                        <p class="text-muted">วันนี้มีรายการรอตรวจสอบ <span class="badge bg-danger rounded-pill"><?php echo $count_order; ?> ออเดอร์</span></p>
                        <div class="mt-4">
                            <a href="products.php" class="btn btn-outline-primary rounded-pill px-4 me-2">จัดการสินค้า</a>
                            <a href="orders.php" class="btn btn-primary rounded-pill px-4">ดูออเดอร์ทั้งหมด</a>
                        </div>
                    </div>
                    <div class="col-md-4 bg-light d-none d-md-flex align-items-center justify-content-center">
                         <img src="https://cdn-icons-png.flaticon.com/512/4222/4222031.png" width="120" class="opacity-50">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i> รายการสั่งซื้อล่าสุด</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dashboardTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>เลขที่ออเดอร์</th>
                                    <th>วันที่/เวลา</th>
                                    <th>ลูกค้า</th>
                                    <th>ยอดรวม</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_latest = "SELECT o.*, u.u_name FROM orders o 
                                               LEFT JOIN user_login u ON o.u_id = u.u_id 
                                               ORDER BY o.or_id DESC LIMIT 50";
                                $res_latest = mysqli_query($conn, $sql_latest);
                                while ($row = mysqli_fetch_array($res_latest)) {
                                    // กำหนดสี Badge
                                    $badge = "bg-secondary";
                                    if($row['or_status'] == 'รอชำระเงิน') $badge = "bg-warning text-dark";
                                    elseif($row['or_status'] == 'ชำระเงินแล้ว') $badge = "bg-info text-white";
                                    elseif($row['or_status'] == 'จัดส่งแล้ว') $badge = "bg-success";
                                    elseif($row['or_status'] == 'ยกเลิก') $badge = "bg-danger";
                                ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $row['or_id']; ?></td>
                                    <td class="small"><?php echo date('d/m/Y H:i', strtotime($row['or_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['u_name']); ?></td>
                                    <td class="fw-bold text-dark">฿<?php echo number_format($row['or_total_amount'], 2); ?></td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2" style="font-size: 0.75rem;">
                                            <?php echo $row['or_status']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="orders.php" class="btn btn-sm btn-light border rounded-circle">
                                            <i class="fa-solid fa-arrow-right text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // ตั้งค่า DataTables
        $('#dashboardTable').DataTable({
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
            },
            "order": [[ 0, "desc" ]] 
        });
    });

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'ออกจากระบบหรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = 'logout.php'; }
        })
    }
</script>
</body>
</html>
