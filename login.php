<?php
session_start();
include_once("config/connectdb.php");

if (isset($_POST["login"])) {
    $email = mysqli_real_escape_string($conn, $_POST['u_email']);
    $password = $_POST['psw'];

    // ค้นหาจาก u_email เท่านั้น
    $sql = "SELECT * FROM user_login WHERE u_email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // ตรวจสอบรหัสผ่าน Hash
        if (password_verify($password, $row['u_password'])) {
            $_SESSION['user_id'] = $row['u_id'];
            $_SESSION['user_name'] = $row['u_name'];
            
            echo "<script>alert('เข้าสู่ระบบสำเร็จ'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง');</script>";
        }
    } else {
        echo "<script>alert('ไม่พบอีเมลนี้ในระบบ');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - MeatShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6">MeatShop Login</h2>
        <form method="post" class="space-y-6">
            <div>
                <label class="block mb-1 font-bold">อีเมล</label>
                <input type="email" name="u_email" placeholder="example@mail.com" required class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block mb-1 font-bold">รหัสผ่าน</label>
                <input type="password" name="psw" placeholder="กรอกรหัสผ่าน" required class="w-full p-2 border rounded">
            </div>
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">เข้าสู่ระบบ</button>
            <p class="text-center text-sm mt-4">ยังไม่มีบัญชี? <a href="register.php" class="text-blue-600">สมัครที่นี่</a></p>
        </form>
    </div>
</body>
</html>
