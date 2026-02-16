<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MSU Ni-sit Shop - สมัครสมาชิก</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-800 min-h-screen flex items-center justify-center py-12 px-4">
  <div class="w-full max-w-lg"> <div class="bg-white rounded-lg shadow-2xl p-8">
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">MeatShop</h1>
      <p class="text-center text-gray-600 mb-8">สร้างบัญชีผู้ใช้ใหม่</p>
      
      <form method="post" action="" class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">ชื่อ-นามสกุล</label>
          <input type="text" name="u_name" placeholder="กรอกชื่อและนามสกุล" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">อีเมล (ใช้สำหรับเข้าสู่ระบบ)</label>
          <input type="email" name="u_email" placeholder="example@mail.com" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">เบอร์โทรศัพท์</label>
          <input type="text" name="u_phone" placeholder="กรอกเบอร์โทรศัพท์" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">รหัสผ่าน</label>
            <input type="password" name="u_password" placeholder="อย่างน้อย 6 ตัว" required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">ยืนยันรหัสผ่าน</label>
            <input type="password" name="psw_confirm" placeholder="ยืนยันรหัสผ่าน" required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
          </div>
        </div>

        <button type="submit" name="register"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 transform hover:scale-105 shadow-lg mt-4">
          ยืนยันการสมัครสมาชิก
        </button>

        <div class="text-center mt-6">
          <span class="text-sm text-gray-600">มีบัญชีอยู่แล้ว? </span>
          <a href="login.php" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">เข้าสู่ระบบ</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

<?php 
if (isset($_POST["register"])) {
    include_once("../config/connectdb.php"); // ตรวจสอบ Path ให้ถูกต้อง

    $u_name     = mysqli_real_escape_string($conn, $_POST['u_name']);
    $u_email    = mysqli_real_escape_string($conn, $_POST['u_email']);
    $u_phone    = mysqli_real_escape_string($conn, $_POST['u_phone']);
    $u_add      = mysqli_real_escape_string($conn, $_POST['u_add']);
    $password   = $_POST['u_password'];
    $confirm    = $_POST['psw_confirm'];

    // 1. ตรวจสอบรหัสผ่านตรงกัน
    if ($password !== $confirm) {
        echo "<script>Swal.fire('ผิดพลาด', 'รหัสผ่านไม่ตรงกัน!', 'error');</script>";
    } 
    // 2. ตรวจสอบความยาว
    else if (strlen($password) < 6) {
        echo "<script>Swal.fire('ผิดพลาด', 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร', 'warning');</script>";
    }
    else {
        // 3. ตรวจสอบอีเมลซ้ำ (ใช้ u_email เป็นหลักในการ Login)
        $check_email = "SELECT u_email FROM user_login WHERE u_email = '$u_email'";
        $result = mysqli_query($conn, $check_email);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>Swal.fire('ผิดพลาด', 'อีเมลนี้ถูกใช้งานแล้ว!', 'error');</script>";
        } else {
            // 4. บันทึกข้อมูล (u_id เป็น Auto-Increment ไม่ต้องระบุ)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO user_login (u_name, u_email, u_password, u_phone, u_add) 
                    VALUES ('$u_name', '$u_email', '$hashed_password', '$u_phone', '$u_add')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>
                    Swal.fire('สำเร็จ', 'สมัครสมาชิกเรียบร้อยแล้ว!', 'success').then(() => {
                        window.location.href = 'login.php';
                    });
                </script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>