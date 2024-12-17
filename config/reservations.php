<?php
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

// ตั้งค่า Time Zone ให้เป็นเวลาประเทศไทย
date_default_timezone_set("Asia/Bangkok");

if (!$conn) {
    die("เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . mysqli_connect_error());
}

// ฟังก์ชันสำหรับส่งข้อความ Broadcast ผ่าน LINE Messaging API
function sendBroadcastMessage($message, $accessToken) {
    $url = "https://api.line.me/v2/bot/message/broadcast"; // Endpoint สำหรับ Broadcast Message
    $data = [
        'messages' => [
            [
                'type' => 'text',
                'text' => $message
            ]
        ]
    ];
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('LINE Broadcast API Error: ' . curl_error($ch));
        return false;
    }
    curl_close($ch);

    return $response;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('กรุณาเข้าสู่ระบบก่อนทำการจอง'); window.location.href = 'login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $room_id = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
    $reservation_date = filter_input(INPUT_POST, 'reservation_date', FILTER_SANITIZE_STRING);
    $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_STRING);
    $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    $third_person_name = filter_input(INPUT_POST, 'third_person_name', FILTER_SANITIZE_STRING);

    if (!$room_id || !$reservation_date || !$start_time || !$end_time) {
        echo "<script>alert('ข้อมูลไม่ครบถ้วน'); window.history.back();</script>";
        exit;
    }

    if ($start_time >= $end_time) {
        echo "<script>alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด'); window.history.back();</script>";
        exit;
    }

    // ตรวจสอบช่วงเวลาการจอง
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS count
        FROM reservations
        WHERE room_id = ? 
          AND reservation_date = ?
          AND (
              (start_time < ? AND end_time > ?) OR
              (start_time < ? AND end_time > ?) OR
              (start_time >= ? AND end_time <= ?)
          )
    ");
    $stmt->bind_param("isssssss", $room_id, $reservation_date, $end_time, $start_time, $end_time, $start_time, $start_time, $end_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo "<script>alert('ไม่สามารถจองได้: ช่วงเวลานี้มีการจองแล้ว'); window.history.back();</script>";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO reservations (user_id, room_id, reservation_date, start_time, end_time, notes, third_person_name, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $status = 'รอการอนุมัติ'; // เพิ่มสถานะเริ่มต้น
        $stmt->bind_param("iissssss", $user_id, $room_id, $reservation_date, $start_time, $end_time, $notes, $third_person_name, $status);

        if ($stmt->execute()) {
            // ดึงชื่อผู้ใช้จากฐานข้อมูล
            $user_stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $user = $user_result->fetch_assoc();
            $user_name = $user['name'];

            // ดึงชื่อห้องจากฐานข้อมูล
            $room_stmt = $conn->prepare("SELECT room_name FROM rooms WHERE room_id = ?");
            $room_stmt->bind_param("i", $room_id);
            $room_stmt->execute();
            $room_result = $room_stmt->get_result();
            $room = $room_result->fetch_assoc();
            $room_name = $room['room_name'];

            // เพิ่มวันที่และเวลาที่จอง
            $current_date_time = date("Y-m-d H:i:s"); // เวลาปัจจุบันในฟอร์แมต "YYYY-MM-DD HH:MM:SS" ตามเวลาประเทศไทย

            // ส่งข้อความผ่าน LINE Messaging API แบบ Broadcast
            $accessToken = "gGH8Og3j09IABlRERbaOpAZ1rlZhrwp+7HWKyGaGSa9ouNqwkaexXcD5JUEOFQG2Zr3f1xsKqah7tSglCmGADjFr9c2LqH9bZRyFo3G39Z/3JI8q4lX3t4Gqq4y9tqcxFpHXoyFzbfYaHWbH14PYSwdB04t89/1O/w1cDnyilFU="; // ใส่ Access Token จาก LINE Developers
            $message = "📌 คำขอจองใหม่:\n" .
                       "ชื่อผู้ใช้: " . $user_name . "\n" .
                       "ห้อง: " . $room_name . "\n" .
                       "วันที่จอง: " . $reservation_date . "\n" .
                       "เวลา: " . $start_time . " - " . $end_time . "\n" .
                       "หมายเหตุ: " . $notes . "\n" .
                       "บุคคลที่ 3: " . $third_person_name . "\n" .
                       "📅 วันที่และเวลาที่ทำการจอง: " . $current_date_time; // เพิ่มข้อมูลวันที่และเวลาที่จอง
            $response = sendBroadcastMessage($message, $accessToken);

            if ($response === false) {
                echo "<script>alert('บันทึกการจองสำเร็จ แต่ไม่สามารถส่งข้อความไปยัง LINE ได้'); window.location.href = '../book.php';</script>";
            } else {
                echo "<script>alert('บันทึกการจองสำเร็จ!'); window.location.href = '../book.php';</script>";
            }
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . htmlspecialchars($stmt->error) . "'); window.history.back();</script>";
        }
    }

    $stmt->close();
} else {
    echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
}

$conn->close();
?>
