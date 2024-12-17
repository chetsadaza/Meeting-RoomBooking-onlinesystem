<?php
session_start(); // ต้องใส่ไว้ด้านบนสุด
include('./config/db.php'); // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, phone, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $name, $phone, $hashed_password, $role);
    $stmt->fetch();

    if ($hashed_password && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_role'] = $role;

        header("Location: ha.php"); // ไปยังหน้าหลัก
        exit();
    } else {
        echo "<script>
                alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง');
                window.history.back();
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
