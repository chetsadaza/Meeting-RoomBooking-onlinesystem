<?php
session_start();
session_destroy(); // ลบข้อมูลใน session
header("Location: login.html"); // ส่งกลับไปหน้า login
exit();
?>
