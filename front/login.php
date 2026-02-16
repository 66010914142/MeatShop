<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบ</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-800 min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-lg shadow-2xl p-8">
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">MeatShop</h1>
      
      <form method="post" action="" class="space-y-6">
        <!-- Username Input -->
        <div>
          <label for="uname" class="block text-lg font-semibold text-gray-700 mb-2"> <b>ชื่อผู้ใช้</b>
          </label>
          <input type="text" placeholder="กรอกชื่อผู้ใช้" name="uname" value="" required autofocus
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          >
        </div>

        <!-- Password Input -->
        <div>
          <label for="psw" class="block text-lg font-semibold text-gray-700 mb-2">
            <b>รหัสผ่าน</b>
          </label>
          <input type="password" placeholder="กรอกรหัสผ่าน" name="psw" value="" required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
          >
        </div>
        <!-- Submit Button -->
        <button 
          type="submit" name ="submit" value="POST"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 transform hover:scale-105"
        >
          เข้าสู่ระบบ
        </button>

        <!--Forgot Password -->
          <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
            ลืมรหัสผ่าน?
          </a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

<?php 
//รับค่าจากฟอร์ม
if (isset($_POST["submit"])) {
    $username = $_POST['uname'];
    $password = $_POST['psw'];

    // ตรวจสอบข้อมูลผู้ใช้ แค่ลองใช้
    if ($username == "admin" && $password == "123456") {
        echo "<script>alert('เข้าสู่ระบบสำเร็จ!');</script>";
        // สามารถ redirect ไปยังหน้าหลักได้
        // header("Location: dashboard.php");
    } else {
        echo "<script>alert('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');</script>";
    }
}
?>