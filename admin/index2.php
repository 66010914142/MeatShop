<?php
    include_once("check_login.php");
    include_once("connectdb.php"); 

    $current_page = basename($_SERVER['PHP_SELF']);

    // --- 1. ดึงข้อมูลตัวเลข Card สถิติ ---
    $count_prod = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(P_id) as total FROM products"))['total'] ?? 0;
    $count_order = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(or_id) as total FROM orders WHERE or_status = 'รอชำระเงิน'"))['total'] ?? 0;
    $total_sales = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(or_total_amount) as total FROM orders WHERE or_status = 'จัดส่งแล้ว'"))['total'] ?? 0;

    // --- 2. เตรียมข้อมูลสำหรับกราฟ (แยกตามสถานะ) ---
    $sql_chart = "SELECT or_status, SUM(or_total_amount) as amount FROM orders GROUP BY or_status";
    $res_chart = mysqli_query($conn, $sql_chart);
    $chart_labels = [];
    $chart_data = [];
    while($row = mysqli_fetch_array($res_chart)){
        $chart_labels[] = $row['or_status'];
        $chart_data[] = $row['amount'];
    }
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
        .sidebar { min-height: 100vh; background-color: #212529; color: white; transition: 0.3s; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link:hover { background-color: #343a40; color: #fff; padding-left: 25px; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; border-radius: 0 50px 50px 0; margin-right: 10px; }
        .main-content { padding: 25px; }
        .stat-card { border: none; border-radius: 15px; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-5px); }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar p-0 shadow position-fixed">
            <div class="p-4 text-center fw-bold border-bottom border-secondary">
                <i class="fa-solid fa-store me-2 text-info"></i> MEAT SHOP
            </div>
            <div class="nav flex-column nav-pills mt-3">
                <a href="index2.php" class="nav-link <?php echo ($current_page == 'index2.php') ? 'active' : ''; ?>"><i class="fa-solid fa-home me-2"></i> หน้าหลักแอดมิน</a>
                <a href="categories.php" class="nav-link"><i class="fa-solid fa-layer-group me-2"></i> จัดการประเภทสินค้า</a>
                <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์</a>
                <a href="customers.php" class="nav-link"><i class="fa-solid fa-users me-2"></i> จัดการลูกค้า</a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="#" class="nav-link text-danger" onclick="confirmLogout(event)"><i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ</a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content" style="margin-left: 16.66667% !important;">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark">ยินดีต้อนรับ, <?php echo $_SESSION['a.name'] ?? 'Admin'; ?></h2>
                    <p class="text-muted mb-0">นี่คือภาพรวมของร้านค้าประจำวันนี้</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-white text-dark shadow-sm p-2 rounded-pill border">
                        <i class="fa-regular fa-calendar-check me-1 text-primary"></i> <?php echo date('d F Y'); ?>
                    </span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body p-4 text-center">
                            <i class="fa-solid fa-box-open fa-2x mb-2 opacity-50"></i>
                            <h6 class="text-uppercase small fw-bold opacity-75">สินค้าทั้งหมด</h6>
                            <h2 class="fw-bold"><?php echo number_format($count_prod); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body p-4 text-center">
                            <i class="fa-solid fa-dollar-sign fa-2x mb-2 opacity-50"></i>
                            <h6 class="text-uppercase small fw-bold opacity-75">ยอดขายที่สำเร็จ</h6>
                            <h2 class="fw-bold">฿<?php echo number_format($total_sales, 2); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-warning text-dark">
                        <div class="card-body p-4 text-center">
                            <i class="fa-solid fa-spinner fa-2x mb-2 opacity-50"></i>
                            <h6 class="text-uppercase small fw-bold opacity-75">ออเดอร์รอตรวจสอบ</h6>
                            <h2 class="fw-bold"><?php echo number_format($count_order); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0">สรุปยอดเงินแยกตามสถานะ (฿)</h6></div>
                        <div class="card-body">
                            <canvas id="salesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-custom h-100 text-center p-4">
                        <h6 class="fw-bold mb-3">เป้าหมายยอดขาย</h6>
                        <canvas id="statusDoughnut"></canvas>
                        <div class="mt-3 small text-muted">คำนวณจากออเดอร์ทุกประเภทในระบบ</div>
                    </div>
                </div>
            </div>

            <div class="card card-custom mb-5">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>ออเดอร์ล่าสุด</h5>
                    <a href="orders.php" class="btn btn-sm btn-primary rounded-pill px-3">ดูออเดอร์ทั้งหมด</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="orderTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#Order ID</th>
                                    <th>ลูกค้า</th>
                                    <th>ยอดชำระ</th>
                                    <th>สถานะ</th>
                                    <th>วันที่</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $latest = mysqli_query($conn, "SELECT o.*, u.u_name FROM orders o LEFT JOIN user_login u ON o.u_id = u.u_id ORDER BY o.or_id DESC LIMIT 10");
                                while($row = mysqli_fetch_array($latest)){
                                    $st = $row['or_status'];
                                    $bg = ($st=='จัดส่งแล้ว'?'bg-success':($st=='รอชำระเงิน'?'bg-warning text-dark':($st=='ยกเลิก'?'bg-danger':'bg-info')));
                                ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $row['or_id']; ?></td>
                                    <td><?php echo $row['u_name']; ?></td>
                                    <td class="fw-bold text-primary">฿<?php echo number_format($row['or_total_amount'], 2); ?></td>
                                    <td><span class="badge <?php echo $bg; ?> rounded-pill"><?php echo $st; ?></span></td>
                                    <td class="small"><?php echo date('d/m/Y', strtotime($row['or_date'])); ?></td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Bar Chart สรุปยอดเงิน
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'ยอดรวมเงิน (บาท)',
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                borderRadius: 10
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // 2. Doughnut Chart
    const ctxDonut = document.getElementById('statusDoughnut').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545']
            }]
        }
    });

    // 3. DataTables
    $(document).ready(function() {
        $('#orderTable').DataTable({
            "pageLength": 5,
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json" }
        });
    });

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'ออกจากระบบหรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => { if (result.isConfirmed) { window.location.href = 'logout.php'; } });
    }
</script>
</body>
</html>
