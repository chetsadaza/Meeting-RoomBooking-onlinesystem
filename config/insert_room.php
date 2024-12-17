<?php
$servername = "sql207.infinityfree.com";  // MySQL Hostname
$username = "if0_37934260";               // MySQL Username
$password = "04249910zaZA";         // MySQL Password
$dbname = "if0_37934260_db_t";            // Database Name
$port = 3306;                             // MySQL Port

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = $conn->real_escape_string($_POST['room_name']);
    $capacity = (int)$_POST['capacity'];
    $location = $conn->real_escape_string($_POST['location']);

    // เพิ่มข้อมูลในตาราง
    $sql = "INSERT INTO rooms (room_name, capacity, location) VALUES ('$room_name', $capacity, '$location')";

    if ($conn->query($sql) === TRUE) {
        // ใช้ JavaScript ในการแจ้งเตือนและกลับไปหน้า editroom.html
        echo "<script>
            alert('เพิ่มข้อมูลสำเร็จ!');
            window.location.href = '../ha.php';
        </script>";
    } else {
        echo "<script>
            alert('เกิดข้อผิดพลาด: " . $conn->error . "');
            window.location.href = '../ha.php';
        </script>";
    }
} else {
    echo "<script>
        alert('ไม่ได้รับข้อมูลจากฟอร์ม');
        window.location.href = '../ha.php';
    </script>";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
