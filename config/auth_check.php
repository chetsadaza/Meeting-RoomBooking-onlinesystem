<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_email'])) {
    // หากยังไม่ได้ล็อกอิน ให้ส่งกลับไปที่ login.html
    header("Location: login.html");
    exit();
}
?>
