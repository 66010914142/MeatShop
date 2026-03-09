<?php
session_start();
unset($_SESSION['cart']); // ลบตะกร้าทั้งหมด
header("Location: cart.php");
exit();
?>