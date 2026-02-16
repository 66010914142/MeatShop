<?php 
// 1. เปิดการแสดง Error (ลบออกเมื่อใช้งานจริงได้แล้ว)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once ("../partials/header.php"); 
include_once("../config/connectdb.php");

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("<div class='alert alert-danger'>การเชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error() . "</div>");
}

// 2. รับค่าจาก URL และจัดการค่าว่าง
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$cat_id = isset($_GET['cat_id']) ? mysqli_real_escape_string($conn, trim($_GET['cat_id'])) : '';

// 3. ตั้งค่าการแบ่งหน้า
$per_page = 16; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
if ($page <= 0) $page = 1;
$start = ($page - 1) * $per_page; 

// 4. สร้างเงื่อนไข SQL (WHERE)
$conditions = [];
if ($search !== "") {
    $conditions[] = "P_name LIKE '%$search%'";
}
if ($cat_id !== "") {
    $conditions[] = "C_id = '$cat_id'";
}

$where_clause = "";
if (count($conditions) > 0) {
    $where_clause = " WHERE " . implode(" AND ", $conditions);
}

// 5. นับจำนวนทั้งหมด
// หมายเหตุ: ตรวจสอบชื่อตาราง products ว่าเป็นตัวเล็กหรือใหญ่ให้ตรงกับในฐานข้อมูล
$sql_total = "SELECT COUNT(*) as total FROM `products` $where_clause";
$rs_total = mysqli_query($conn, $sql_total);

if (!$rs_total) {
    die("<div class='alert alert-danger'>SQL Error (Count): " . mysqli_error($conn) . "</div>");
}

$row_total = mysqli_fetch_assoc($rs_total);
$total_records = $row_total['total'];
$total_pages = ceil($total_records / $per_page); 

// 6. ดึงข้อมูลสินค้า
$sql = "SELECT * FROM `products` $where_clause LIMIT $start, $per_page";
$rs = mysqli_query($conn, $sql);

if (!$rs) {
    die("<div class='alert alert-danger'>SQL Error (Select): " . mysqli_error($conn) . "</div>");
}
?>

<div class="container mt-4">
    <div class="category-filter mb-4 text-center">
        <p class="fw-bold mb-2">เลือกหมวดหมู่สินค้า</p>
        <div class="btn-group flex-wrap shadow-sm" role="group">
            <a href="index.php" class="btn <?= ($cat_id == '') ? 'btn-secondary' : 'btn-outline-secondary' ?>">ทั้งหมด</a>
            <a href="?cat_id=mc01" class="btn <?= ($cat_id == 'mc01') ? 'btn-secondary' : 'btn-outline-secondary' ?>">เนื้อสัตว์แช่แข็ง</a>
            <a href="?cat_id=sc02" class="btn <?= ($cat_id == 'sc02') ? 'btn-secondary' : 'btn-outline-secondary' ?>">อาหารทะเลแช่แข็ง</a>
            <a href="?cat_id=rc04" class="btn <?= ($cat_id == 'rc04') ? 'btn-secondary' : 'btn-outline-secondary' ?>">อาหารพร้อมทาน</a>
            <a href="?cat_id=fc03" class="btn <?= ($cat_id == 'fc03') ? 'btn-secondary' : 'btn-outline-secondary' ?>">อาหารสำเร็จรูป</a>
            <a href="?cat_id=ic05" class="btn <?= ($cat_id == 'ic05') ? 'btn-secondary' : 'btn-outline-secondary' ?>">แป้งและวัตถุดิบ</a>
            <a href="?cat_id=dc06" class="btn <?= ($cat_id == 'dc06') ? 'btn-secondary' : 'btn-outline-secondary' ?>">ของหวาน</a>
        </div>
    </div>

    <form action="index.php" method="GET" class="mb-4">
        <input type="hidden" name="cat_id" value="<?= htmlspecialchars($cat_id) ?>">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="พิมพ์ชื่อสินค้า..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">🔍 ค้นหา</button>
            <?php if($search != "" || $cat_id != ""): ?>
                <a href="index.php" class="btn btn-outline-secondary">ล้างการตั้งค่า</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($search != "" || $cat_id != ""): ?>
        <p class="mb-3">ผลการค้นหา: พบสินค้าทั้งหมด <strong><?= $total_records ?></strong> รายการ</p>
    <?php endif; ?>

    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;"> 
        <?php 
        if (mysqli_num_rows($rs) > 0) {
            while($data = mysqli_fetch_array($rs)) { 
        ?>
            <div class="product-card border p-3 rounded shadow-sm text-center">
                <img src="../img/<?= htmlspecialchars($data['P_id']) ?>.<?= htmlspecialchars($data['P_img']) ?>" 
                     alt="<?= htmlspecialchars($data['P_name']) ?>" 
                     style="width: 100%; height: 200px; object-fit: cover;"
                     onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                
                <div class="product-info mt-3">
                    <h5 class="product-name"><?= htmlspecialchars($data['P_name']) ?></h5>
                    <div class="price text-danger fw-bold h5">฿<?= number_format($data['P_price'], 2) ?></div>
                    <div class="stock text-muted small mb-3">คงเหลือ: <?= $data['P_amonut'] ?> ชิ้น</div>
                    
                    <div class="d-grid gap-2">
                        <a href="product_detail.php?id=<?= $data['P_id'] ?>" class="btn btn-outline-primary btn-sm">รายละเอียด</a> 
                        <button class="btn btn-success btn-sm">เพิ่มลงตะกร้า</button>
                    </div>
                </div>
            </div>
        <?php 
            } 
        } else {
            echo "<div class='col-12 text-center py-5'>
                    <h4 class='text-muted'>ไม่พบสินค้าในรายการ</h4>
                    <p>ลองเปลี่ยนคำค้นหาหรือเลือกหมวดหมู่ใหม่อีกครั้ง</p>
                  </div>";
        }
        ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <?php 
            $params = $_GET;
            unset($params['page']);
            $query_string = http_build_query($params);
            $url_params = ($query_string != "") ? "&" . $query_string : "";
            ?>

            <?php if($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?><?= $url_params ?>">ก่อนหน้า</a></li>
            <?php endif; ?>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $url_params ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?><?= $url_params ?>">ถัดไป</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
