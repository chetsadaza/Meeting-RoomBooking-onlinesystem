<?php
$servername = "sql207.infinityfree.com";  // MySQL Hostname
$username = "if0_37934260";               // MySQL Username
$password = "04249910zaZA";         // MySQL Password
$dbname = "if0_37934260_db_t";            // Database Name
$port = 3306;                             // MySQL Port

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection failed: " . $conn->connect_error;
    exit;
}

// รับค่าจาก POST
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// สร้าง SQL Statement
$sql = "INSERT INTO Users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')";
$stmt = $conn->prepare($sql);

// ตรวจสอบความยาวและรูปแบบของรหัสผ่าน
if (strlen($password) <= 6 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) {
    echo "รหัสผ่านต้องมีมากกว่า 6 ตัวอักษรและมีทั้งตัวพิมพ์ใหญ่และตัวพิมพ์เล็ก";
    http_response_code(400);
    exit;
}
// ตรวจสอบว่าอีเมลมี '@'
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "อีเมลไม่ถูกต้อง";
    http_response_code(400); // ส่งสถานะ HTTP 400 (Bad Request)
    exit;
}
if ($stmt) {
    $stmt->bind_param("ssss", $name, $email, $phone, $password);
    if ($stmt->execute()) {
        // ดึง user_id ที่เพิ่งถูกสร้าง
        $user_id = $stmt->insert_id;
        echo "Registration successful! User ID: " . $user_id;
    } else {
        http_response_code(400);
        if ($conn->errno == 1062) {
            echo "อีเมลนี้ลงทะเบียนแล้ว";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo "Failed to prepare the statement: " . $conn->error;
}

$conn->close();
?>
