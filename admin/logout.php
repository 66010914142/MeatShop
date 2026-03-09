<?php
	session_start();
	
	unset($_SESSION['a.id']);
	unset($_SESSION['a.name']);
	
	echo "<script>";
	echo "window.location='index.php';";
	echo "</script>";
	
?>

<meta charset="utf-8">
