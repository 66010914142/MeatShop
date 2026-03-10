<?php
    include_once("check_login.php");
    include_once("connectdb.php");

    // 1. ดึงสถิติจริงจาก Database
    $sql_count_today = "SELECT COUNT(or_id) as total FROM orders WHERE DATE(or_date) = CURRENT_DATE()";
    $res_today = mysqli_query($conn, $sql_count_today);
    $count_today = mysqli_fetch_array($res_today)['total'] ?? 0;

    // นับออเดอร์ที่มีการแจ้งโอนแล้ว (รอตรวจ)
    $sql_count_wait = "SELECT COUNT(or_id) as total FROM orders WHERE or_status = 'รอชำระเงิน' AND or_slip_img IS NOT NULL AND or_slip_img != ''";
    $res_wait = mysqli_query($conn, $sql_count_wait);
    $count_wait = mysqli_fetch_array($res_wait)['total'] ?? 0;
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการออเดอร์ - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Sarabun', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 15px 20px; transition: 0.3s; }
        .sidebar .nav-link:hover { background-color: #343a40; color: #fff; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        .main-content { padding: 20px; }
        .table-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .slip-preview { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.2s; }
        .slip-preview:hover { transform: scale(1.1); border-color: #0d6efd; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0 shadow">
            <div class="p-4 text-center fw-bold border-bottom border-secondary">
                <i class="fa-solid fa-store me-2 text-info"></i> MEAT SHOP
            </div>
            <div class="nav flex-column nav-pills mt-2">
                <a href="index2.php" class="nav-link"><i class="fa-solid fa-home me-2"></i> หน้าหลักแอดมิน</a>
                <a href="categories.php" class="nav-link"><i class="fa-solid fa-layer-group me-2"></i> จัดการประเภทสินค้า</a>
                <a href="products.php" class="nav-link"><i class="fa-solid fa-box me-2"></i> จัดการสินค้า</a>
                <a href="orders.php" class="nav-link active"><i class="fa-solid fa-cart-shopping me-2"></i> จัดการออเดอร์</a>
                <a href="customers.php" class="nav-link"><i class="fa-solid fa-users me-2"></i> จัดการลูกค้า</a>
                <hr class="mx-3 my-2" style="border-color: #444;">
                <a href="logout.php" class="nav-link text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ</a>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2 class="fw-bold"><i class="fa-solid fa-clipboard-list me-2 text-primary"></i>ระบบจัดการออเดอร์</h2>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-white p-3 border-0 shadow-sm border-start border-primary border-4">
                        <small class="text-muted d-block">ออเดอร์วันนี้</small>
                        <h4 class="mb-0 fw-bold"><?php echo $count_today; ?> รายการ</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-white p-3 border-0 shadow-sm border-start border-warning border-4">
                        <small class="text-muted d-block">แจ้งโอนแล้ว (รอตรวจ)</small>
                        <h4 class="mb-0 fw-bold text-warning"><?php echo $count_wait; ?> รายการ</h4>
                    </div>
                </div>
            </div>

            <div class="card table-card bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 text-start">เลขที่ออเดอร์</th>
                                    <th>ลูกค้า/ที่อยู่</th>
                                    <th>สลิป</th>
                                    <th>ยอดรวม</th>
                                    <th>สถานะการสั่งซื้อ</th>
                                    <th>รายละเอียด</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT o.*, u.u_name, u.u_add FROM orders o 
                                        LEFT JOIN user_login u ON o.u_id = u.u_id 
                                        ORDER BY o.or_date DESC";
                                $rs = mysqli_query($conn, $sql);
                                while ($data = mysqli_fetch_array($rs)) {
                                ?>
                                <tr>
                                    <td class="ps-4 text-start">
                                        <span class="fw-bold text-primary">#<?php echo $data['or_id']; ?></span>
                                        <div class="text-muted small" style="font-size: 0.75rem;"><?php echo date('d/m/Y H:i', strtotime($data['or_date'])); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($data['u_name']); ?></div>
                                        <button class="btn btn-sm text-primary p-0 border-0 bg-transparent text-decoration-underline" 
                                                style="font-size: 0.8rem;"
                                                onclick="viewAddress('<?php echo htmlspecialchars($data['u_name']); ?>', '<?php echo addslashes(htmlspecialchars($data['u_add'] ?? "")); ?>')">
                                            <i class="fa-solid fa-location-dot me-1"></i>ดูที่อยู่ส่งของ
                                        </button>
                                    </td>
                                    <td>
                                        <?php if (!empty($data['or_slip_img'])) { 
                                            $slip_path = "../slips/" . $data['or_slip_img'];
                                        ?>
                                            <img src="<?php echo $slip_path; ?>" class="slip-preview shadow-sm" onclick="viewSlip('<?php echo $slip_path; ?>')">
                                        <?php } else { echo '<span class="text-muted small">-</span>'; } ?>
                                    </td>
                                    <td class="fw-bold">฿<?php echo number_format($data['or_total_amount'], 2); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm w-auto d-inline-block rounded-pill border-secondary" 
                                                onchange="updateStatus('<?php echo $data['or_id']; ?>', this.value)"
                                                style="font-size: 0.85rem;">
                                            <option value="รอชำระเงิน" <?php if($data['or_status'] == 'รอชำระเงิน') echo 'selected'; ?>>รอชำระเงิน</option>
                                            <option value="ชำระเงินแล้ว" <?php if($data['or_status'] == 'ชำระเงินแล้ว') echo 'selected'; ?>>ชำระเงินแล้ว</option>
                                            <option value="จัดส่งแล้ว" <?php if($data['or_status'] == 'จัดส่งแล้ว') echo 'selected'; ?>>จัดส่งแล้ว</option>
                                            <option value="ยกเลิก" <?php if($data['or_status'] == 'ยกเลิก') echo 'selected'; ?>>ยกเลิก</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm" onclick="viewOrderItems('<?php echo $data['or_id']; ?>')">
                                            <i class="fa-solid fa-magnifying-glass me-1"></i> สินค้า
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteOrder('<?php echo $data['or_id']; ?>')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
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

<div class="modal fade" id="orderItemsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-dark text-white rounded-top-4 border-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-box-open me-2 text-warning"></i>รายละเอียดออเดอร์ <span id="modal_or_id"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="modal_content"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function viewAddress(name, address) {
        Swal.fire({
            title: 'ที่อยู่จัดส่ง: ' + name,
            text: address ? address : 'ลูกค้ายังไม่ได้ระบุที่อยู่',
            icon: 'info',
            confirmButtonText: 'รับทราบ',
            confirmButtonColor: '#0d6efd'
        });
    }

    function viewSlip(imgUrl) {
        Swal.fire({ 
            imageUrl: imgUrl, 
            imageAlt: 'หลักฐานการชำระเงิน',
            showConfirmButton: false, 
            showCloseButton: true,
            width: 'auto',
            padding: '1em'
        });
    }

    function updateStatus(orId, newStatus) {
        Swal.fire({
            title: 'ยืนยันการเปลี่ยนสถานะ?',
            text: "ต้องการเปลี่ยนสถานะออเดอร์ #" + orId + " เป็น '" + newStatus + "' หรือไม่?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#0d6efd'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'update_status.php?id=' + orId + '&status=' + encodeURIComponent(newStatus);
            } else {
                location.reload(); 
            }
        });
    }

    function viewOrderItems(orId) {
        $('#modal_or_id').text('#' + orId);
        $('#modal_content').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-2">กำลังดึงข้อมูลสินค้า...</p></div>');
        $('#orderItemsModal').modal('show');

        $.ajax({
            url: 'get_admin_order_items.php',
            type: 'GET',
            data: { or_id: orId },
            success: function(res) {
                $('#modal_content').html(res);
            },
            error: function() {
                $('#modal_content').html('<div class="alert alert-danger m-3">ไม่สามารถดึงข้อมูลได้</div>');
            }
        });
    }

    // ฟังก์ชันลบข้อมูลออเดอร์
    function deleteOrder(orId) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "ต้องการลบออเดอร์ #" + orId + " ใช่หรือไม่? ข้อมูลสินค้าในออเดอร์นี้จะถูกลบออกทั้งหมด!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ยืนยันการลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete_order.php?id=' + orId;
            }
        });
    }
</script>
</body>
</html>
