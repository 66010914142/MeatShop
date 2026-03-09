<?php
	session_start();
			unset($_SESSION['user_id']);
			unset($_SESSION['user_name']);
			echo "<script>";
            echo "alert('ออกจากระบบเรียบร้อยแล้ว');";
			echo "window.location='index.php';";
			echo "</script>";
	?>