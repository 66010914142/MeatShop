<?php 
require_once ("../partials/header.php"); 
include_once("../config/connectdb.php");

// 1. รับค่าจาก URL ทั้งคำค้นหา (search) และ รหัสหมวดหมู่ (cat_id)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$cat_id = isset($_GET['cat_id']) ? mysqli_real_escape_string($conn, $_GET['cat_id']) : '';

// 2. ตั้งค่าการแบ่งหน้า
$per_page = 16; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$start = ($page - 1) * $per_page; 

// 3. สร้างเงื่อนไข SQL (WHERE) ให้รองรับทั้งค้นหาชื่อและรหัสหมวดหมู่
$conditions = [];
if ($search != "") {
    $conditions[] = "P_name LIKE '%$search%'";
}
if ($cat_id != "") {
    $conditions[] = "C_id = '$cat_id'"; // กรองตามรหัสหมวดหมู่ที่คุณส่งมา (เช่น mc01)
}

$where_clause = "";
if (count($conditions) > 0) {
    $where_clause = " WHERE " . implode(" AND ", $conditions);
}

// นับจำนวนทั้งหมดเพื่อทำ Pagination
$sql_total = "SELECT COUNT(*) as total FROM `products` $where_clause";
$rs_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($rs_total);
$total_records = $row_total['total'];
$total_pages = ceil($total_records / $per_page); 

// 4. ดึงข้อมูลสินค้าตามเงื่อนไข
$sql = "SELECT * FROM `products` $where_clause LIMIT $start, $per_page";
$rs = mysqli_query($conn, $sql);
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
        <p>พบสินค้าทั้งหมด <?= $total_records ?> รายการ</p>
    <?php endif; ?>

    <div class="product-grid"> 
        <?php 
        if ($rs && mysqli_num_rows($rs) > 0) {
            while($data = mysqli_fetch_array($rs)) { 
        ?>
            <div class="product-card">
                <img src="../img/<?= $data['P_id'] ?>.<?= $data['P_img'] ?>" alt="รูปสินค้า" class="product-img">
                <div class="product-info">
                    <div class="product-name"><?= $data['P_name'] ?></div>
                    <div class="price">฿<?= number_format($data['P_price'], 2) ?></div>
                    <div class="stock">มีสินค้าในสต็อก: <?= $data['P_amonut'] ?> ชิ้น</div>
                    <a href="product_detail.php?id=<?= $data['P_id'] ?>" class="btn-add">รายละเอียดสินค้า</a> 
                    <button class="btn-add">เพิ่มลงตะกร้า</button>
                </div>
            </div>
        <?php 
            } 
        } else {
            echo "<div class='col-12 text-center p-5'><p>ไม่พบสินค้าในหมวดหมู่นี้</p></div>";
        }
        ?>
    </div>

    <div class="pagination mt-5 justify-content-center">
        <?php 
        $params = $_GET; // ดึงค่า Parameter ทั้งหมดจาก URL
        unset($params['page']); // ลบค่า page เดิมออก
        $query_string = http_build_query($params); // สร้าง query string ใหม่ (เช่น search=หมู&cat_id=mc01)
        $url_params = ($query_string != "") ? "&" . $query_string : "";
        ?>

        <?php if($page > 1): ?>
            <a href="?page=<?= $page - 1 ?><?= $url_params ?>">&laquo; ก่อนหน้า</a>
        <?php endif; ?>

        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?><?= $url_params ?>" class="<?= ($page == $i) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?><?= $url_params ?>">ถัดไป &raquo;</a>
        <?php endif; ?>
    </div>
</div>