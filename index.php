<?php 
// 1. จัดการเรื่อง Error (เปิดไว้เพื่อดูว่ามีอะไรผิดพลาดบน Server จริงหรือไม่)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once ("partials/header.php"); 
include_once("config/connectdb.php");

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("<div class='alert alert-danger'>เชื่อมต่อ DB ไม่ได้: " . mysqli_connect_error() . "</div>");
}

// 2. รับค่าจาก URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$cat_id = isset($_GET['cat_id']) ? mysqli_real_escape_string($conn, trim($_GET['cat_id'])) : '';

// 3. ตั้งค่าการแบ่งหน้า
$per_page = 16; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$start = ($page - 1) * $per_page; 

// 4. สร้างเงื่อนไขการค้นหา (Logic: ถ้าเข้าหน้าแรกเงื่อนไขจะเป็นค่าว่าง จะดึงสินค้าทั้งหมดทันที)
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

// 5. นับจำนวน (ใช้ชื่อตาราง Products ตัว P ใหญ่ตามรูปของคุณ)
$sql_total = "SELECT COUNT(*) as total FROM `products` $where_clause";
$rs_total = mysqli_query($conn, $sql_total);
$total_records = 0;
if ($rs_total) {
    $row_total = mysqli_fetch_assoc($rs_total);
    $total_records = $row_total['total'];
}
$total_pages = ceil($total_records / $per_page); 

// 6. ดึงข้อมูลสินค้า (ใช้ชื่อตาราง Products ตัว P ใหญ่)
$sql = "SELECT * FROM `products` $where_clause LIMIT $start, $per_page";
$rs = mysqli_query($conn, $sql);

if (!$rs) {
    die("<div class='alert alert-danger'>SQL Error: " . mysqli_error($conn) . "</div>");
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
        </div>
    </form>

    <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php 
        if (mysqli_num_rows($rs) > 0) {
            while($data = mysqli_fetch_array($rs)) { 
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="img/<?= $data['P_id'] ?>.<?= $data['P_img'] ?>" 
                         class="card-img-top" 
                         style="height: 200px; object-fit: cover;"
                         onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                    
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($data['P_name']) ?></h5>
                        <p class="text-danger fw-bold h5">฿<?= number_format($data['P_price'], 2) ?></p>
                        <p class="small text-muted">คงเหลือ: <?= $data['P_amonut'] ?> ชิ้น</p>
                        
                        <div class="d-grid gap-2">
                            <a href="product_detail.php?id=<?= $data['P_id'] ?>" class="btn btn-sm btn-outline-primary">รายละเอียด</a>
                            <button class="btn btn-sm btn-success">ลงตะกร้า</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            } 
        } else {
            echo "<div class='col-12 text-center py-5'><h4>ไม่พบสินค้าในรายการ</h4></div>";
        }
        ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <?php 
            $params = $_GET; unset($params['page']);
            $url_params = http_build_query($params);
            $url_params = ($url_params != "") ? "&" . $url_params : "";
            ?>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $url_params ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
