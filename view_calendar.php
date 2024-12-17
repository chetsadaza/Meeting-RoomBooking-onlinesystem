<?php
session_start();
include('./config/db.php'); // เชื่อมต่อฐานข้อมูล



// ดึงชื่อผู้ใช้จากฐานข้อมูล
$stmt_user = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
$stmt_user->bind_param("i", $_SESSION['user_id']);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// ดึงข้อมูลห้องจากฐานข้อมูล
$stmt_rooms = $conn->prepare("SELECT room_id, room_name FROM rooms");
$stmt_rooms->execute();
$rooms = $stmt_rooms->get_result();

?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meeting RoomBooking onlinesystem</title>
  <link rel="stylesheet" href="./css/book.css">


<!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




<style>
/* ตั้งค่าข้อความเหตุการณ์ใน FullCalendar */
.fc-event {
    color: #000 !important; /* เปลี่ยนสีข้อความเป็นสีดำ */
    text-decoration: none !important; /* เอาขีดเส้นใต้ (underline) ออก */
    font-weight: normal !important; /* ตั้งค่าความหนาของข้อความตามปกติ */
}
</style>



<head>
  <style>
    /* เปลี่ยนสีของข้อความในเหตุการณ์ใน FullCalendar */
    .fc-event {
        color: #000 !important; /* เปลี่ยนข้อความเป็นสีดำ */
    }

    /* เปลี่ยนสีข้อความส่วนหัวของปฏิทิน เช่น ชื่อเดือน */
    .fc-toolbar-title {
        color: #000 !important; /* เปลี่ยนชื่อเดือนเป็นสีดำ */
    }

    /* เปลี่ยนสีของวันในปฏิทิน */
    .fc-daygrid-day-number {
        color: #000 !important; /* เปลี่ยนตัวเลขวันที่เป็นสีดำ */
    }
    
  </style>
</head>



<head>
  <style>
    /* เอาขีดเส้นใต้ข้อความออก */
    .fc-event {
        text-decoration: none !important;
        color: #000 !important;
    }

    .fc-toolbar-title {
        text-decoration: none !important;
    }

   

    .fc-daygrid-day-number {
        text-decoration: none !important;
        color: #000 !important;
    }
  </style>
</head>



</head>

<body>






    <div id="calendar"></div>

    

<!-- Modal -->
<div id="eventModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modal-title" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>ชื่อผู้ใช้:</strong> <span id="modal-user"></span></p>
                <p><strong>เวลาเริ่มต้น:</strong> <span id="modal-start"></span></p>
                <p><strong>เวลาสิ้นสุด:</strong> <span id="modal-end"></span></p>
                <p><strong>หมายเหตุ:</strong> <span id="modal-notes"></span></p>
                <p><strong>ข้อมูลบุคคลที่ 3:</strong> <span id="modal-third-person"></span></p>
                <p><strong>สถานะ:</strong> <span id="modal-status"></span></p> <!-- เพิ่มสถานะ -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>








  </div>
  <script src="./js/book.js"></script>
  <script src="./js/book1.js"></script>

</body>

</html>