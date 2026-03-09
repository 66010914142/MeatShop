<?php
session_start();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login: พาฤดี ปูนจีน (จ๋า)</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --accent-color: #6610f2;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        /* ตกแต่งพื้นหลังด้วยวงกลมเบลอๆ ให้ดูมีมิติ */
        body::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--primary-color);
            filter: blur(150px);
            border-radius: 50%;
            top: 10%;
            left: 10%;
            opacity: 0.3;
            z-index: -1;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .login-header i {
            font-size: 3rem;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .btn-primary {
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            border: none;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
        }

        .alert {
            border-radius: 12px;
            font-size: 0.9rem;
            border: none;
        }

        .brand-subtitle {
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #94a3b8;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>

<div class="login-card">
    <div class="login-header text-center">
        <i class="fa-solid fa-user-shield"></i>
        <h3 class="fw-bold text-dark mb-1">เข้าสู่ระบบหลังบ้าน</h3>
        <p class="brand-subtitle mb-4">ร้านอาหารเเช่เเข็ง</p>
    </div>
    
    <form method="post" action="">
        <div class="mb-3">
            <label for="auser" class="form-label"><i class="fa-solid fa-envelope me-2"></i>Username</label>
            <input type="text" class="form-control" id="auser" name="auser" placeholder="กรอกชื่อผู้ใช้" autofocus required>
        </div>
        <div class="mb-4">
            <label for="apwd" class="form-label"><i class="fa-solid fa-lock me-2"></i>Password</label>
            <input type="password" class="form-control" id="apwd" name="apwd" placeholder="กรอกรหัสผ่าน" required>
        </div>
        <div class="d-grid">
            <button type="submit" name="Submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-right-to-bracket me-2"></i>เข้าสู่ระบบ
            </button>
        </div>
    </form>

    <?php
    if (isset($_POST['Submit'])) {
        include_once("connectdb.php");
        
        $user = $_POST['auser'];
        $pwd  = $_POST['apwd'];

        $stmt = mysqli_prepare($conn, "SELECT a_id, a_name, a_password FROM admin WHERE a_username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($data = mysqli_fetch_array($result)) {
            if ($pwd == $data['a_password']) {
                $_SESSION['a.id'] = $data['a_id'];
                $_SESSION['a.name'] = $data['a_name'];
                
                echo "<div class='alert alert-success mt-4 text-center'><i class='fa-solid fa-circle-check me-2'></i>เข้าสู่ระบบสำเร็จ! กำลังไป...</div>";
                echo "<script>setTimeout(function(){ window.location='index2.php'; }, 1500);</script>";
            } else {
                echo "<div class='alert alert-danger mt-4 text-center'><i class='fa-solid fa-circle-xmark me-2'></i>รหัสผ่านไม่ถูกต้อง</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-4 text-center'><i class='fa-solid fa-circle-xmark me-2'></i>ไม่พบชื่อผู้ใช้นี้</div>";
        }
        mysqli_stmt_close($stmt);
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>