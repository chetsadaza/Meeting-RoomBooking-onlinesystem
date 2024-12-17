<?php
session_start();
include('./config/db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_email'])) {
    $_SESSION['alert'] = "กรุณาเข้าสู่ระบบก่อน!";
    header("Location: edit.php");
    exit();
}

// รับค่าจากฟอร์ม
$new_name = $_POST['name'];
$new_email = $_POST['email'];
$new_phone = $_POST['phone'];

// ตรวจสอบว่าได้รับข้อมูลครบถ้วน
if (empty($new_name) || empty($new_email) || empty($new_phone)) {
    $_SESSION['alert'] = "กรุณากรอกข้อมูลให้ครบถ้วน!";
    header("Location: edit.php");
    exit();
}

// อัปเดตข้อมูลในฐานข้อมูล
$sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['alert'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง: " . $conn->error;
    header("Location: edit.php");
    exit();
}

$stmt->bind_param("ssss", $new_name, $new_email, $new_phone, $_SESSION['user_email']);

if ($stmt->execute()) {
    // อัปเดต SESSION และแจ้งเตือน
    $_SESSION['user_name'] = $new_name;
    $_SESSION['user_email'] = $new_email;
    $_SESSION['user_phone'] = $new_phone;
    $_SESSION['alert'] = "อัปเดตข้อมูลสำเร็จ!";
    header("Location: ha.php");
} else {
    $_SESSION['alert'] = "เกิดข้อผิดพลาด: " . $stmt->error;
    header("Location: edit.php");
}

$stmt->close();
$conn->close();
?>
