<?php
    include_once("check_login.php");
    include_once("connectdb.php"); 

    $current_page = basename($_SERVER['PHP_SELF']);

    // --- 1. ดึงข้อมูล Card สถิติ ---
    $count_prod = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(P_id) as total FROM products"))['total'] ?? 0;
    $count_order = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(or_id) as total FROM orders WHERE or_status = 'รอชำระเงิน'"))['total'] ?? 0;
    $total_sales = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(or_total_amount) as total FROM orders WHERE or_status = 'จัดส่งแล้ว'"))['total'] ?? 0;

    // --- 2. ข้อมูลกราฟยอดขายตามสถานะ (Bar Chart) ---
    $sql_status = "SELECT or_status, SUM(or_total_amount) as amount FROM orders GROUP BY or_status";
    $res_status = mysqli_query($conn, $sql_status);
    $status_labels = [];
    $status_data = [];
    while($row = mysqli_fetch_array($res_status)){
        $status_labels[] = $row['or_status'];
        $status_data[] = $row['amount'];
    }

    // --- 3. ข้อมูลกราฟสินค้าขายดี 5 อันดับ (Top 5 Products) ---
    // ดึงชื่อสินค้า และผลรวมของจำนวนที่ขายได้ (d_qty) จากตารางลูก
    $sql_top = "SELECT p.P_name, SUM(d.d_qty) as total_qty 
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
    <title>Dashboard - Top Selling Products</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { width: 250px; min-height: 100vh; background-color: #212529; color: white; position: fixed; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: 0.3s; }
        .card-custom:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="p-4 text-center fw-bold border-bottom border-secondary">
        <i class="fa-solid fa-store me-2 text-info"></i> MEAT SHOP
    </div>
    <div class="nav flex-column mt-3">
        <a href="index2.php" class="nav-link active"><i class="fa-solid fa-home me-2"></i> หน้าหลัก</a>
        <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> สินค้า</a>
        <a href="orders.php" class="nav-link"><i class="fa-solid fa-cart-shopping me-2"></i> ออเดอร์</a>
        <a href="logout.php" class="nav-link text-danger"><i class="fa-solid fa-power-off me-2"></i> ออกจากระบบ</a>
    </div>
</div>

<div class="main-content">
    <h3 class="fw-bold mb-4">รายงานสรุปผลการดำเนินงาน</h3>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-custom bg-white p-4 border-start border-primary border-5">
                <small class="text-muted fw-bold">สินค้าในร้าน</small>
                <h2 class="fw-bold mb-0 text-primary"><?php echo number_format($count_prod); ?> รายการ</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-white p-4 border-start border-success border-5">
                <small class="text-muted fw-bold">รายได้รวมที่สำเร็จ</small>
                <h2 class="fw-bold mb-0 text-success">฿<?php echo number_format($total_sales, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-white p-4 border-start border-warning border-5">
                <small class="text-muted fw-bold">ออเดอร์รอดำเนินการ</small>
                <h2 class="fw-bold mb-0 text-warning"><?php echo number_format($count_order); ?> รายการ</h2>
            </div>
        </div>
    </div>

    
    <div class="row mb-4 g-4">
        <div class="col-lg-7">
            <div class="card card-custom bg-white p-4">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-crown text-warning me-2"></i> 5 อันดับสินค้าขายดี (ตามจำนวนชิ้น)</h5>
                <canvas id="topProductsChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-custom bg-white p-4">
                <h5 class="fw-bold mb-4">สัดส่วนยอดขายตามสถานะ</h5>
                <canvas id="statusDonutChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card card-custom bg-white">
        <div class="card-body">
            <h5 class="fw-bold mb-3">ออเดอร์ล่าสุด</h5>
            <table id="orderTable" class="table table-hover align-middle w-100">
                <thead>
                    <tr class="table-light">
                        <th>ออเดอร์</th>
                        <th>ลูกค้า</th>
                        <th>ยอดสุทธิ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $latest = mysqli_query($conn, "SELECT o.*, u.u_name FROM orders o LEFT JOIN user_login u ON o.u_id = u.u_id ORDER BY o.or_id DESC LIMIT 5");
                    while($row = mysqli_fetch_array($latest)){
                    ?>
                    <tr>
                        <td class="fw-bold">#<?php echo $row['or_id']; ?></td>
                        <td><?php echo $row['u_name']; ?></td>
                        <td class="text-primary fw-bold">฿<?php echo number_format($row['or_total_amount'], 2); ?></td>
                        <td><span class="badge bg-info rounded-pill"><?php echo $row['or_status']; ?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    // 1. กราฟสินค้าขายดี (Horizontal Bar Chart)
    const ctxTop = document.getElementById('topProductsChart').getContext('2d');
    new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($prod_labels); ?>,
            datasets: [{
                label: 'จำนวนชิ้นที่ขายได้',
                data: <?php echo json_encode($prod_qty); ?>,
                backgroundColor: '#0d6efd',
                borderRadius: 5,
            }]
        },
        options: {
            indexAxis: 'y', // ทำให้เป็นกราฟแนวนอน
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    // 2. กราฟสถานะ (Donut Chart)
    const ctxDonut = document.getElementById('statusDonutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_data); ?>,
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                hoverOffset: 10
            }]
        }
    });

    $(document).ready(function() {
        $('#orderTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json" }
        });
    });
</script>
</body>
</html>
