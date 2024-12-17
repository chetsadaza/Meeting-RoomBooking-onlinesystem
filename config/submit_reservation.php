<?php
session_start();
include('db_connection.php'); // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $room_id = $_POST['room_id'];
    $reservation_date = $_POST['reservation_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO reservations (user_id, room_id, reservation_date, start_time, end_time, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $user_id, $room_id, $reservation_date, $start_time, $end_time, $notes);

    if ($stmt->execute()) {
        echo "การจองสำเร็จ!";
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }
}
?>
