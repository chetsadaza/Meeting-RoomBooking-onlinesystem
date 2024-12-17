function toggleForm() {
    const container = document.getElementById('reservation-container');
    
    if (container.style.display === "block") {
        // ซ่อนฟอร์ม: เลื่อนกลับไปขอบจอก่อน แล้วค่อยซ่อน
        container.style.right = "-500px";
        setTimeout(() => {
            container.style.display = "none";
        }, 300); // รอให้ transition ทำงานเสร็จ (0.3 วินาที)
        document.body.style.overflow = ""; // เปิดการเลื่อนหน้าจอ
    } else {
        // แสดงฟอร์ม: เปิดการแสดงผลก่อน แล้วเลื่อนออกมา
        container.style.display = "block";
        setTimeout(() => {
            container.style.right = "0";
        }, 10); // หน่วงเวลาเล็กน้อยเพื่อให้ transition ทำงาน
        document.body.style.overflow = "hidden"; // ปิดการเลื่อนหน้าจอ
    }
}

// ฟังก์ชัน Drag & Drop สำหรับปุ่มบวก
const dragButton = document.getElementById('drag-button');
let offsetX = 0;
let offsetY = 0;

dragButton.onmousedown = function (event) {
    dragButton.classList.add('dragging');

    // เก็บตำแหน่งเริ่มต้นของเมาส์
    offsetX = event.clientX - dragButton.getBoundingClientRect().left;
    offsetY = event.clientY - dragButton.getBoundingClientRect().top;

    // ฟังก์ชันเคลื่อนย้ายปุ่ม
    function moveAt(pageX, pageY) {
        dragButton.style.left = pageX - offsetX + 'px';
        dragButton.style.top = pageY - offsetY + 'px';
    }

    // ฟังค์ชันเลื่อนปุ่มตามการเคลื่อนของเมาส์
    function onMouseMove(event) {
        moveAt(event.pageX, event.pageY);
    }

    document.addEventListener('mousemove', onMouseMove);

    dragButton.onmouseup = function () {
        dragButton.classList.remove('dragging');
        document.removeEventListener('mousemove', onMouseMove);
        dragButton.onmouseup = null;
    };
};

// ปิด drag default ของเบราว์เซอร์
dragButton.ondragstart = function () {
    return false;
};




