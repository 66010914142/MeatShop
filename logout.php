<?php
	session_start();
			unset($_SESSION['u_id']);
			unset($_SESSION['u_name']);
			echo "<script>";
            echo "alert('ออกจากระบบเรียบร้อยแล้ว');";
			echo "window.location='index.php';";
			echo "</script>";
	?>
