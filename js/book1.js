document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // การแสดงผลเริ่มต้น
        locale: 'th', // ตั้งค่าภาษาไทย
        events: './config/fetch_events.php', // ดึงข้อมูลเหตุการณ์จาก PHP
        headerToolbar: {
            left: 'prev,next today', // ปุ่มด้านซ้าย
            center: 'title', // ชื่อเดือนตรงกลาง
            right: 'dayGridMonth,timeGridWeek,timeGridDay' // ปุ่มเปลี่ยนมุมมอง
        },
        eventColor: '#ff5722', // สีพื้นหลังของเหตุการณ์ (สีส้ม)
        eventTextColor: '#ffffff', // สีข้อความของเหตุการณ์ (สีขาว)

        // กำหนดการทำงานเมื่อคลิกเหตุการณ์
        eventClick: function (info) {
            var event = info.event;

            // ตั้งค่าข้อมูลใน Modal
            document.getElementById('modal-title').innerText = event.extendedProps.room_name || 'รายละเอียดการจอง';
            document.getElementById('modal-user').innerText = event.extendedProps.user_name || 'ไม่ทราบชื่อผู้ใช้';
            document.getElementById('modal-start').innerText = event.start.toLocaleString();
            document.getElementById('modal-end').innerText = event.end ? event.end.toLocaleString() : '-';
            document.getElementById('modal-notes').innerText = event.title;
            document.getElementById('modal-third-person').innerText = event.extendedProps.third_person_name || 'ไม่มีข้อมูลบุคคลที่ 3';
            document.getElementById('modal-status').innerText = event.extendedProps.status || 'ไม่มีสถานะ'; // เพิ่มสถานะ
            var modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        }
        
    });
    

    // แสดงปฏิทิน
    calendar.render();
});






