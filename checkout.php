<?php
session_start();
include_once("config/connectdb.php");
if (empty($_SESSION['cart'])) { header("Location: index.php"); exit(); }

$total_price = 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สรุปรายการสั่งซื้อ - MeatShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="container mx-auto max-w-2xl bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold mb-6 border-b pb-2">สรุปรายการสั่งซื้อ</h2>
        
        <table class="w-full mb-6">
            <?php foreach ($_SESSION['cart'] as $p_id => $qty): 
                $res = mysqli_query($conn, "SELECT * FROM products WHERE P_id = '$p_id'");
                $row = mysqli_fetch_array($res);
                $sum = $row['P_price'] * $qty;
                $total_price += $sum;
            ?>
            <tr class="border-b">
                <td class="py-2"><?= $row['P_name'] ?> x <?= $qty ?></td>
                <td class="text-right font-bold">฿<?= number_format($sum, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="text-xl font-bold text-red-600">
                <td class="py-4">ยอดชำระทั้งสิ้น</td>
                <td class="text-right">฿<?= number_format($total_price, 2) ?></td>
            </tr>
        </table>

        <div class="bg-blue-50 p-6 rounded-xl mb-6 text-center">
            <p class="font-bold text-blue-800">โอนเงินผ่าน PromptPay</p>
            <img src="https://promptpay.io/0812345678/<?= $total_price ?>.png" class="mx-auto w-48 my-4">
            <p class="text-sm text-gray-600">MeatShop</p>
        </div>

        <form action="confirm_order.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-bold text-gray-700 mb-2">แนบหลักฐานการโอน (บังคับแนบสลิป) *</label>
                <input type="file" name="slip_img" class="w-full border p-2 rounded-lg bg-gray-50" accept="image/*" required>
            </div>
            <button type="submit" name="btn_confirm" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-green-700 transition shadow-lg">
                ยืนยันการสั่งซื้อและแจ้งโอนเงิน
            </button>
        </form>
    </div>
</body>
</html>
