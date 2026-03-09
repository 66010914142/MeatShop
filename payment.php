<?php
session_start();
include_once("config/connectdb.php");
include_once("partials/header.php");

// 1. รับค่า or_id จาก URL
if (!isset($_GET['order_id'])) {
    echo "<script>alert('ไม่พบรหัสคำสั่งซื้อ'); window.location='profile.php';</script>";
    exit();
}

$or_id = mysqli_real_escape_string($conn, $_GET['order_id']);
$sql = "SELECT * FROM orders WHERE or_id = '$or_id'";
$rs = mysqli_query($conn, $sql);
$order = mysqli_fetch_array($rs);

if (!$order) {
    echo "<script>alert('ไม่พบข้อมูลคำสั่งซื้อ'); window.location='profile.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ชำระเงิน - MeatShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
            
            <div class="w-full md:w-1/2 bg-blue-600 p-10 text-white flex flex-col justify-center items-center">
                <div class="mb-6 text-center">
                    <h2 class="text-3xl font-black mb-2 tracking-tight">ชำระเงิน</h2>
                    <p class="text-blue-100 opacity-80">คำสั่งซื้อเลขที่ #<?= $or_id ?></p>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-inner mb-6">
                    <img src="https://promptpay.io/0812345678/<?= $order['or_total_amount'] ?>.png" 
                         alt="PromptPay QR Code" class="w-64 h-64 object-contain">
                </div>

                <div class="text-center space-y-2">
                    <p class="text-sm uppercase tracking-widest text-blue-200">ยอดเงินที่ต้องโอน</p>
                    <p class="text-5xl font-black tracking-tighter">฿<?= number_format($order['or_total_amount'], 2) ?></p>
                    <div class="pt-4 text-blue-100 text-sm">
                        <p class="font-bold text-white">ตะวันฉาย สวัสดิ์พาณิชย์</p>
                        <p>พร้อมเพย์: 081-234-5678</p>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-10 bg-white">
                <form action="payment_db.php" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">
                    <input type="hidden" name="or_id" value="<?= $or_id ?>">
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">แจ้งหลักฐานการโอน</h3>
                    
                    <div class="flex-grow space-y-6">
                        <div class="relative group">
                            <label class="block text-sm font-semibold text-gray-600 mb-2">ภาพสลิปการโอนเงิน</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all duration-300 cursor-pointer relative overflow-hidden h-64 flex flex-col items-center justify-center group">
                                <input type="file" name="slip_img" id="slip_img" 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" 
                                       accept="image/*" required onchange="previewImage(this)">
                                
                                <div id="preview-text" class="space-y-3">
                                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto transition-transform group-hover:scale-110">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-gray-700 font-bold">คลิกเพื่ออัปโหลดสลิป</p>
                                        <p class="text-xs text-gray-400">รองรับไฟล์ JPG, PNG</p>
                                    </div>
                                </div>
                                <img id="preview-img" class="hidden absolute inset-0 w-full h-full object-contain p-2 bg-white z-10 rounded-xl">
                            </div>
                        </div>

                        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl">
                            <div class="flex">
                                <div class="flex-shrink-0 text-amber-400">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs text-amber-700 leading-relaxed">
                                        หลังกดยืนยัน แอดมินจะตรวจสอบยอดเงินและเปลี่ยนสถานะเป็น "ชำระเงินแล้ว" ให้โดยเร็วที่สุด
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="btn_pay" 
                            class="mt-8 w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-blue-100 flex items-center justify-center group">
                        ยืนยันแจ้งโอนเงิน
                        <i class="fa-solid fa-chevron-right ms-2 transition-transform group-hover:translate-x-1"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <a href="profile.php" class="text-gray-400 hover:text-gray-600 transition text-sm font-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> กลับไปหน้าประวัติการสั่งซื้อ
            </a>
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('preview-img');
        const text = document.getElementById('preview-text');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                text.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>