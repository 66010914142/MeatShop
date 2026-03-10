<?php
    include_once("check_login.php");
    include_once("connectdb.php"); 

    $current_page = basename($_SERVER['PHP_SELF']);

    // --- 1. ดึงข้อมูล Card สถิติ ---
    // แก้ไข: ใช้ P_id ตามรูป
    $count_prod = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(P_id) as total FROM products"))['total'] ?? 0;
    
    // แก้ไข: ใช้ or_status ตามรูป
    $count_order = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(or_id) as total FROM orders WHERE or_status = 'รอชำระเงิน'"))['total'] ?? 0;
    
    // แก้ไข: ใช้ or_total_amount ตามรูป
    $total_sales = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(or_total_amount) as total FROM orders WHERE or_status = 'จัดส่งแล้ว'"))['total'] ?? 0;

    // --- 2. ข้อมูลกราฟยอดขายตามสถานะ ---
    $sql_status = "SELECT or_status, SUM(or_total_amount) as amount FROM orders GROUP BY or_status";
    $res_status = mysqli_query($conn, $sql_status);
    $status_labels = [];
    $status_data = [];
    while($row = mysqli_fetch_array($res_status)){
        $status_labels[] = $row['or_status'];
        $status_data[] = $row['amount'];
    }

    // --- 3. ข้อมูลกราฟสินค้าขายดี 5 อันดับ ---
    // แก้ไข: ใช้ P_name จากตาราง products และ order_Details (D ตัวใหญ่)
    $sql_top = "SELECT p.P_name, SUM(d.quantity) as total_qty 
                FROM order_Details d 
                JOIN products p ON d.P_id = p.P_id 
                GROUP BY d.P_id 
                ORDER BY total_qty DESC 
                LIMIT 5";
    $res_top = mysqli_query($conn, $sql_top);
    $prod_labels = [];
    $prod_qty = [];
    while($row = mysqli_fetch_array($res_top)){
        $prod_labels[] = $row['P_name'];
        $prod_qty[] = $row['total_qty'];
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
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #212529; color: #fff; position: fixed; }
        .sidebar .nav-link { color: #adb5bd; padding: 15px 25px; }
        .sidebar .nav-link.active { background: #0d6efd; color: #fff; }
        .main-content { margin-left: 260px; padding: 30px; }
        .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .chart-container { background: #fff; padding: 20px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="p-4 text-center border-bottom border-secondary mb-3">
        <h4 class="fw-bold text-info mb-0">MEAT SHOP</h4>
    </div>
    <nav class="nav flex-column">
        <a href="index2.php" class="nav-link active"><i class="fa-solid fa-gauge me-2"></i> หน้าหลัก</a>
        <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> สินค้า</a>
        <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> ออเดอร์</a>
        <a href="logout.php" class="nav-link text-danger mt-5"><i class="fa-solid fa-power-off me-2"></i> ออกจากระบบ</a>
    </nav>
</div>

<div class="main-content">
    <h3 class="fw-bold mb-4 text-dark">ภาพรวมระบบ</h3>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-stats p-4 border-start border-primary border-5">
                <div class="text-muted small fw-bold">รายการสินค้า</div>
                <h2 class="fw-bold mb-0"><?php echo number_format($count_prod); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-4 border-start border-success border-5">
                <div class="text-muted small fw-bold">ยอดขายสำเร็จ</div>
                <h2 class="fw-bold mb-0">฿<?php echo number_format($total_sales, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-4 border-start border-warning border-5">
                <div class="text-muted small fw-bold">รอตรวจสอบ</div>
                <h2 class="fw-bold mb-0 text-warning"><?php echo number_format($count_order); ?></h2>
            </div>
        </div>
    </div>

        <div class="row g-4">
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-star text-warning me-2"></i> 5 อันดับสินค้าขายดี (จำนวนชิ้น)</h5>
                <canvas id="topProductsChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-container text-center">
                <h5 class="fw-bold mb-4">สัดส่วนสถานะออเดอร์</h5>
                <canvas id="statusDonutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // กราฟสินค้าขายดี
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($prod_labels); ?>,
            datasets: [{
                label: 'จำนวนที่ขายได้ (ชิ้น)',
                data: <?php echo json_encode($prod_qty); ?>,
                backgroundColor: '#0d6efd',
                borderRadius: 8
            }]
        },
        options: { indexAxis: 'y', responsive: true }
    });

    // กราฟสถานะ
    new Chart(document.getElementById('statusDonutChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_data); ?>,
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545']
            }]
        }
    });
</script>

</body>
</html>
