$(document).ready(function() {
    // สลับหน้าจอ Login/Register
    $(".veen .rgstr-btn button").click(function() {
        $('.veen .wrapper').addClass('move');
        $('.body').css('background', '#e0b722');
        $(".veen .login-btn button").removeClass('active');
        $(this).addClass('active');
    });

    $(".veen .login-btn button").click(function() {
        $('.veen .wrapper').removeClass('move');
        $('.body').css('background', '#ff4931');
        $(".veen .rgstr-btn button").removeClass('active');
        $(this).addClass('active');
    });

    // จัดการฟอร์ม Register
    document.getElementById("register").addEventListener("submit", handleFormSubmit);
});

// ฟังก์ชันสำหรับจัดการการส่งฟอร์ม
async function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    // ตรวจสอบอีเมล
    const email = formData.get("email");
    if (!email.includes("@")) {
        showNotification("อีเมลไม่ถูกต้อง ต้องมี '@'", "error");
        return; // หยุดการส่งฟอร์ม
    }

    // ตรวจสอบความยาวและรูปแบบของรหัสผ่าน
    const password = formData.get("password");
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z]).{7,}$/; // อย่างน้อย 7 ตัวอักษร มีตัวพิมพ์ใหญ่และพิมพ์เล็ก
    if (!passwordRegex.test(password)) {
        showNotification("รหัสผ่านต้องมีมากกว่า 6 ตัวอักษรและมีทั้งตัวพิมพ์ใหญ่และตัวพิมพ์เล็ก", "error");
        return; // หยุดการส่งฟอร์ม
    }

    try {
        const response = await fetch(form.action, {
            method: form.method,
            body: formData
        });

        const result = await response.text();
        if (response.ok) {
            showNotification("สมัครสมาชิกสำเร็จ!", "success");
            form.reset();
        } else {
            showNotification(result, "error");
        }
    } catch (error) {
        showNotification("เกิดข้อผิดพลาด: " + error.message, "error");
    }
}



// ฟังก์ชันสำหรับแสดงการแจ้งเตือน
function showNotification(message, type) {
    const notification = document.getElementById("notification");

    if (!notification) {
        console.error("Element #notification not found in DOM.");
        return;
    }

    // ตั้งค่าข้อความและประเภท
    notification.textContent = message;
    notification.className = ""; // รีเซ็ตคลาส
    notification.classList.add(type === "success" ? "success" : "error");
    notification.style.display = "inline-block";
    notification.style.opacity = "1";

    // ซ่อนข้อความหลังจาก 3 วินาที
    setTimeout(() => {
        notification.style.opacity = "0";
        setTimeout(() => {
            notification.style.display = "none";
        }, 500); // ให้เวลา animation opacity
    }, 3000);
}







