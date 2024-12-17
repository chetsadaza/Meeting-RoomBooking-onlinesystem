<?php
include 'db.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากตาราง reservations พร้อม JOIN กับ users และ rooms
$sql = "
    SELECT 
        reservations.reservation_id,
        users.name AS user_name,
        rooms.room_name,
        reservations.reservation_date,
        reservations.start_time,
        reservations.end_time,
        reservations.notes,
        reservations.third_person_name,
        reservations.status -- เพิ่มสถานะ
    FROM reservations
    JOIN users ON reservations.user_id = users.user_id
    JOIN rooms ON reservations.room_id = rooms.room_id
";


$result = mysqli_query($conn, $sql);

$events = [];

// วนลูปข้อมูลจากฐานข้อมูล
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'id' => $row['reservation_id'],
        'user_name' => $row['user_name'], // ชื่อผู้ใช้
        'room_name' => $row['room_name'], // ชื่อห้อง
        'third_person_name' => $row['third_person_name'], // ชื่อบุคคลที่ 3
        'title' => $row['notes'], // หมายเหตุ
        'start' => $row['reservation_date'] . 'T' . $row['start_time'], // วันเวลาเริ่มต้น
        'end' => $row['reservation_date'] . 'T' . $row['end_time'], // วันเวลาสิ้นสุด
        'status' => $row['status'] // เพิ่มสถานะ
    ];
}


// ส่งข้อมูลในรูปแบบ JSON
header('Content-Type: application/json');
echo json_encode($events);

// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($conn);
?>
