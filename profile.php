<?php
ob_start();
session_start();
include_once("config/connectdb.php");

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้งาน
$user_query = mysqli_query($conn, "SELECT * FROM user_login WHERE u_id = '$u_id'");
$user = mysqli_fetch_array($user_query);

// ดึงประวัติการสั่งซื้อ (อ้างอิงชื่อคอลัมน์ตามโครงสร้างจริงของคุณ)
$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE u_id = '$u_id' ORDER BY or_date DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>โปรไฟล์ของฉัน - MeatShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal { transition: opacity 0.25s ease; }
        body.modal-active { overflow: hidden; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-10">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">จัดการบัญชีผู้ใช้งาน</h1>
            <a href="index.php" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition flex items-center shadow-md">
                <i class="fa-solid fa-house me-2"></i> กลับหน้าแรก
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-fit">
                <h2 class="text-xl font-bold mb-5 border-b pb-2 text-gray-700">ข้อมูลส่วนตัว</h2>
                <form action="update_profile.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">ชื่อผู้ใช้งาน</label>
                        <input type="text" name="u_name" value="<?php echo $user['u_name']; ?>" class="w-full border border-gray-300 p-2.5 rounded-lg mt-1 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">อีเมล (เปลี่ยนไม่ได้)</label>
                        <input type="text" value="<?php echo $user['u_email']; ?>" class="w-full border border-gray-300 p-2.5 rounded-lg mt-1 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">เบอร์โทรศัพท์</label>
                        <input type="text" name="u_phone" value="<?php echo $user['u_phone']; ?>" class="w-full border border-gray-300 p-2.5 rounded-lg mt-1 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600">ที่อยู่จัดส่ง</label>
                        <textarea name="u_add" class="w-full border border-gray-300 p-2.5 rounded-lg mt-1 focus:ring-2 focus:ring-blue-500 outline-none" rows="3"><?php echo $user['u_add']; ?></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition font-bold shadow-lg shadow-blue-200">บันทึกข้อมูล</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold mb-5 border-b pb-2 text-gray-700">ประวัติการสั่งซื้อ</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b text-gray-500 text-sm uppercase tracking-wider">
                                <th class="p-4 font-semibold">เลขที่สั่งซื้อ</th>
                                <th class="p-4 font-semibold">วันที่</th>
                                <th class="p-4 text-right font-semibold">ยอดรวม</th>
                                <th class="p-4 text-center font-semibold">สถานะ</th>
                                <th class="p-4 text-center font-semibold">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(mysqli_num_rows($order_query) > 0) { 
                                while($row = mysqli_fetch_array($order_query)) { ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 font-bold text-gray-700">#<?php echo $row['or_id']; ?></td>
                                    <td class="p-4 text-sm text-gray-600"><?php echo date('d/m/Y H:i', strtotime($row['or_date'])); ?></td>
                                    <td class="p-4 font-bold text-gray-900 text-right"><?php echo number_format($row['or_total_amount'], 2); ?> ฿</td>
                                    <td class="p-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold 
                                            <?php 
                                                $status = $row['or_status'];
                                                if($status == 'รอชำระเงิน') echo 'bg-amber-100 text-amber-700';
                                                elseif($status == 'ชำระเงินแล้ว') echo 'bg-emerald-100 text-emerald-700';
                                                else echo 'bg-blue-100 text-blue-700';
                                            ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button onclick="openModal('<?php echo $row['or_id']; ?>')" 
                                                class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-lg hover:bg-blue-600 hover:text-white transition font-semibold text-xs shadow-sm border border-blue-100">
                                            <i class="fa-solid fa-list-ul me-1"></i> รายละเอียด
                                        </button>
                                    </td>
                                </tr>
                            <?php } 
                            } else { ?>
                                <tr><td colspan="5" class="p-10 text-center text-gray-400">ยังไม่มีประวัติการสั่งซื้อสินค้า</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="orderModal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" onclick="closeModal()"></div>
        <div class="modal-container bg-white w-11/12 md:max-w-2xl mx-auto rounded-2xl shadow-2xl z-50 overflow-y-auto">
            <div class="modal-content py-6 text-left px-8">
                <div class="flex justify-between items-center pb-4 border-b">
                    <p class="text-2xl font-bold text-gray-800">รายละเอียดคำสั่งซื้อ <span id="modalOrderId" class="text-blue-600"></span></p>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div id="modalBody" class="my-5 max-h-[60vh] overflow-y-auto">
                    </div>
                <div class="flex justify-end pt-4 border-t">
                    <button onclick="closeModal()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-bold transition">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(orId) {
            const modal = document.getElementById('orderModal');
            document.getElementById('modalOrderId').innerText = '#' + orId;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('modal-active');

            // ดึงข้อมูลผ่าน AJAX
            $.ajax({
                url: 'get_order_details.php',
                type: 'GET',
                data: { or_id: orId },
                success: function(response) {
                    $('#modalBody').html(response);
                },
                error: function() {
                    $('#modalBody').html('<p class="text-red-500 text-center">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>');
                }
            });
        }

        function closeModal() {
            const modal = document.getElementById('orderModal');
            modal.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('modal-active');
            $('#modalBody').html('<div class="flex justify-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>');
        }
    </script>
</body>
</html>