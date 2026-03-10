<?php
    include_once("check_login.php");
    include_once("connectdb.php"); 

    $current_page = basename($_SERVER['PHP_SELF']);

    // --- 1. ดึงข้อมูล Card สถิติ ---
    $count_prod = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(P_id) as total FROM products"))['total'] ?? 0;
    $count_order = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(or_id) as total FROM orders WHERE or_status = 'รอชำระเงิน'"))['total'] ?? 0;
    $total_sales = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(or_total_amount) as total FROM orders WHERE or_status = 'จัดส่งแล้ว'"))['total'] ?? 0;

    // --- 2. ดึงข้อมูลสินค้าขายดี (ปรับตามชื่อคอลัมน์ในรูปภาพของคุณ) ---
    // ใช้ตาราง order_Details และคอลัมน์ quantity
    $sql_top = "SELECT p.P_name, SUM(d.quantity) as total_qty 
                FROM order_Details d 
                JOIN products p ON d.P_id = p.P_id 
                GROUP BY d.P_id 
                ORDER BY total_qty DESC 
                LIMIT 5";
    
    $res_top = mysqli_query($conn, $sql_top);
    $chart_labels = [];
    $chart_data = [];

    if ($res_top && mysqli_num_rows($res_top) > 0) {
        while($row = mysqli_fetch_array($res_top)){
            $chart_labels[] = $row['P_name'];
            $chart_data[] = $row['total_qty'];
        }
    } else {
        $chart_labels = ['ไม่มีข้อมูลการขาย'];
        $chart_data = [0];
    }
?>

<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - MEAT SHOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; width: 250px; position: fixed; z-index: 100; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 15px 20px; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; border-radius: 0 50px 50px 0; }
        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .stat-card { border: none; border-radius: 12px; color: white; }
    </style>
</head>
<body>

<div class="d-flex">
    <nav class="sidebar shadow">
        <div class="p-4 text-center fw-bold text-white border-bottom border-secondary">
            <i class="fa-solid fa-drumstick-bite me-2 text-warning"></i> MEAT SHOP
        </div>
        <div class="nav flex-column mt-3">
            <a href="index2.php" class="nav-link active"><i class="fa-solid fa-chart-pie me-2"></i> แดชบอร์ด</a>
            <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> สินค้า</a>
            <a href="orders.php" class="nav-link"><i class="fa-solid fa-file-invoice-dollar me-2"></i> ออเดอร์</a>
            <a href="logout.php" class="nav-link text-danger mt-5"><i class="fa-solid fa-power-off me-2"></i> ออกจากระบบ</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">ภาพรวมระบบ</h3>
            <span class="text-muted"><i class="fa-regular fa-calendar me-1"></i> <?php echo date('d/m/Y'); ?></span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary p-3">
                    <small class="opacity-75">สินค้าทั้งหมด</small>
                    <h2 class="fw-bold mb-0"><?php echo number_format($count_prod); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success p-3">
                    <small class="opacity-75">ยอดขายสำเร็จ</small>
                    <h2 class="fw-bold mb-0">฿<?php echo number_format($total_sales, 2); ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-warning text-dark p-3">
                    <small class="opacity-75">รอยืนยันชำระเงิน</small>
                    <h2 class="fw-bold mb-0"><?php echo number_format($count_order); ?></h2>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-fire me-2"></i> 5 อันดับสินค้าขายดีที่สุด (ยอดรวมชิ้น)</h5>
                    <div style="height: 300px;">
                        <canvas id="bestSellerChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-3">รายการสั่งซื้อล่าสุด 10 รายการ</h5>
            <div class="table-responsive">
                <table id="orderList" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th>เลขที่ออเดอร์</th>
                            <th>สถานะ</th>
                            <th>ยอดสุทธิ</th>
                            <th>วันที่สั่งซื้อ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY or_id DESC LIMIT 10");
                        while($row = mysqli_fetch_array($orders)){
                            $status_class = "bg-secondary";
                            if($row['or_status'] == 'จัดส่งแล้ว') $status_class = "bg-success";
                            if($row['or_status'] == 'รอชำระเงิน') $status_class = "bg-warning text-dark";
                            if($row['or_status'] == 'ชำระเงินแล้ว') $status_class = "bg-info text-white";
                        ?>
                        <tr>
                            <td class="fw-bold">#<?php echo $row['or_id']; ?></td>
                            <td><span class="badge <?php echo $status_class; ?> rounded-pill px-3"><?php echo $row['or_status']; ?></span></td>
                            <td class="fw-bold text-primary">฿<?php echo number_format($row['or_total_amount'], 2); ?></td>
                            <td class="small"><?php echo $row['or_date']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    // กราฟสินค้าขายดี
    const ctx = document.getElementById('bestSellerChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'จำนวนที่ขายได้ (ชิ้น)',
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.8)',
                borderColor: '#0d6efd',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { drawBorder: false } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // เรียกใช้ DataTable
    $(document).ready(function() {
        $('#orderList').DataTable({
            "paging": false,
            "info": false,
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json" }
        });
    });
</script>
</body>
</html>
