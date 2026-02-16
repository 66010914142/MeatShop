<?php require_once("config/connectdb.php");
require_once __DIR__ . '/partials/header.php';
$code = trim($_GET['code'] ?? '');
$order = null; $items = [];
if ($code !== '') {
$st = $pdo->prepare('SELECT * FROM orders WHERE order_code = ?');
$st->execute([$code]);
$order = $st->fetch();
if ($order) {
$it = $pdo->prepare('SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE order_id = ?');
$it->execute([$order['id']]);
$items = $it->fetchAll();
}
}
?>
<h2>ติดตามคำสั่งซื้อ</h2>
<form class="row g-2 mb-3" method="get">
<div class="col-md-6">
<input class="form-control" type="text" name="code" placeholder="เช่น ABCD1234" value="<?= htmlspecialchars($code) ?>">
</div>
<div class="col-md-3">
<button class="btn btn-primary w-100">ตรวจสอบ</button>
</div>
</form>
<?php if ($code !== '' && !$order): ?>
<div class="alert alert-danger">ไม่พบคำสั่งซื้อ</div>
<?php endif; ?>
<?php if ($order): ?>
<div class="card card-body shadow-sm">
<div class="d-flex justify-content-between align-items-center">
<div>
<div class="fw-bold">รหัสคำสั่งซื้อ: <?= htmlspecialchars($order['order_code']) ?></div>
<div class="text-muted small">สถานะ: <span class="badge bg-info text-dark"><?= htmlspecialchars($order['status']) ?></span></div>
</div>
<div class="text-end small text-muted">สร้างเมื่อ <?= htmlspecialchars($order['created_at']) ?></div>
</div>
<hr>
<?php foreach ($items as $it): ?>
<div class="d-flex justify-content-between">
<span><?= htmlspecialchars($it['name']) ?> × <?= (int)$it['qty'] ?></span>
<span>฿<?= money($it['unit_price']*$it['qty']) ?></span>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>