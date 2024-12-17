<?php 
session_start();

// ตรวจสอบว่ามีการตั้งค่า $_SESSION หรือไม่
if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_email']) || !isset($_SESSION['user_phone'])) {
    echo "กรุณาเข้าสู่ระบบก่อน!";
    exit();
}

$user_name = htmlspecialchars($_SESSION['user_name']);
$user_email = htmlspecialchars($_SESSION['user_email']);
$user_phone = htmlspecialchars($_SESSION['user_phone']);

if (isset($_SESSION['alert'])) {
    echo "<script>alert('" . $_SESSION['alert'] . "');</script>";
    unset($_SESSION['alert']); // ลบข้อความแจ้งเตือนหลังแสดง
}

// สมมติว่าเรามีการเชื่อมต่อฐานข้อมูลอยู่แล้ว
include './config/db.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับค่า user_id จาก session
$user_id = $_SESSION['user_id'];

// เมื่อผู้ใช้ส่งแบบฟอร์มเปลี่ยนรหัสผ่าน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ารหัสผ่านใหม่ตรงตามเงื่อนไข
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z]).{6,}$/', $new_password)) {
        echo "<script>alert('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร และมีทั้งตัวพิมพ์ใหญ่และตัวพิมพ์เล็ก');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน');</script>";
    } else {
        // ตรวจสอบรหัสผ่านเก่าในฐานข้อมูล
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        if (!$stmt) {
            die("เกิดข้อผิดพลาดในคำสั่ง SQL: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($old_password, $user['password'])) {
            // อัปเดตรหัสผ่านใหม่
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            if (!$update_stmt) {
                die("เกิดข้อผิดพลาดในคำสั่ง SQL: " . $conn->error);
            }
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                echo "<script>alert('รหัสผ่านถูกเปลี่ยนเรียบร้อยแล้ว'); window.location.href = 'ha.php';</script>";
                exit();
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน');</script>";
            }
        } else {
            echo "<script>alert('รหัสผ่านเก่าไม่ถูกต้อง');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting RoomBooking onlinesystem</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;700&display=swap">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #9face6);
            font-family: 'Prompt', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
        .container h1 {
            text-align: center;
            color: #333;
            font-size: 26px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 16px;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            border-color: #74ebd5;
            outline: none;
            box-shadow: 0 0 5px rgba(116, 235, 213, 0.5);
        }
        button {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            color: white;
            background-color: #74ebd5;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #5fc3c6;
        }
        p {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }
        .form-group p {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>แก้ไขข้อมูลส่วนตัว</h1>
        <form id="editForm" method="POST" action="update.php">
            <div class="form-group">
                <label for="name">ชื่อ:</label>
                <input type="text" id="name" name="name" placeholder="กรอกชื่อของคุณ" value="<?php echo $user_name; ?>">
            </div>
            <div class="form-group">
                <label for="email">อีเมล:</label>
                <input type="email" id="email" name="email" placeholder="example@email.com" value="<?php echo $user_email; ?>">
            </div>
            <div class="form-group">
                <label for="phone">เบอร์โทร:</label>
                <input type="tel" id="phone" name="phone" placeholder="099999999" value="<?php echo $user_phone; ?>">
            </div>
            <button type="submit">บันทึกข้อมูล</button>
        </form>

        <h1>เปลี่ยนรหัสผ่าน</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="old_password">รหัสผ่านเก่า:</label>
                <input type="password" name="old_password" id="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">รหัสผ่านใหม่:</label>
                <input type="password" name="new_password" id="new_password" required>
                <p>ต้องมีอย่างน้อย 6 ตัวอักษร และมีทั้งตัวพิมพ์ใหญ่และตัวพิมพ์เล็ก</p>
            </div>
            <div class="form-group">
                <label for="confirm_password">ยืนยันรหัสผ่านใหม่:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit">เปลี่ยนรหัสผ่าน</button>
        </form>
    </div>
</body>
</html>
