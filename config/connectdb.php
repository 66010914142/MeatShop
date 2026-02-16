<?php
		$host = "localhost";
		$user = "root";
		$pwd = "12345678";
		$db = "4142db";
		$conn = mysqli_connect($host,$user,$pwd,$db) or die ("เชื่อมต่อฐานข้อมูลไม่ได้");
		mysqli_query($conn, "SET NAMES utf8");
		?>