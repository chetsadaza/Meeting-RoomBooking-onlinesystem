<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['navbar__link'])) {
    session_destroy();
    unset($_SESSION['user_id']);
    header('location: login.html');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting RoomBooking onlinesystem</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #80DADA; /* ใช้สีจากภาพ */
}

        .container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            text-align: left;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }
        input:focus, textarea:focus {
            border-color: #2575fc;
        }
        button {
            background: #2575fc;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #6a11cb;
        }
        .response {
            margin-top: 20px;
            padding: 10px;
            background: #e3fcef;
            border-left: 5px solid #28a745;
            color: #155724;
            border-radius: 8px;
            font-size: 14px;
        }
        .error {
            background: #fdecea;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
        .animated-bg {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                background-color: #f0f0f0;
            }
            50% {
                background-color: #e0e0e0;
            }
        }


        .back-button {
    position: absolute;
    top: 20px;
    left: 20px;
}

.back-button a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffa500; /* สีฟ้า */
    color: #fff; /* สีตัวอักษร */
    padding: 10px 20px;
    border-radius: 50px; /* ขอบโค้งมนเต็ม */
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); /* เงา */
    transition: all 0.3s ease; /* เพิ่มความลื่นไหล */
}

.back-button a:hover {
    background: #fd464a; /* สีเข้มขึ้นเมื่อชี้ */
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); /* เงาเพิ่มขึ้น */
    transform: translateY(-2px); /* ยกปุ่มขึ้น */
}

.back-button a i {
    font-size: 16px; /* ขนาดของไอคอน */
}


    </style>
</head>
<body>
<div class="back-button">
    <a href="ha.php">
        <i class="fas fa-arrow-left"></i> กลับ
    </a>
</div>


    <div class="container">
        <h1>แจ้งปัญหา</h1>
        <form method="POST">
            <label for="name">ชื่อ</label>
            <input type="text" id="name" name="name" placeholder="กรอกชื่อ" required>

            <label for="message">แจ้งปัญหา</label>
            <textarea id="message" name="message" placeholder="กรอกปัญหา" rows="5" required></textarea>

            <button type="submit">ส่ง</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accessToken = 'ryGHsEP5hN8teosn2vVGL7vn40b7BDgdP/QXmQXXxSPnvmVu/fIeZkT79tSBtLoir1LzpUiuBanxHaHURMej4OCtFJhUp9hZWdT78A+BgyMdGG2Fl5WEWkljb8vlHkBT9HCpOSoM/G442P3DaKLVfwdB04t89/1O/w1cDnyilFU='; // ใส่ Token จริง
            $name = htmlspecialchars($_POST['name']);
            $message = htmlspecialchars($_POST['message']);
            $dateTime = date("Y-m-d H:i:s");

            function sendBroadcastMessage($message, $accessToken) {
                $url = "https://api.line.me/v2/bot/message/broadcast";

                $headers = [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $accessToken
                ];

                $data = [
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message
                        ]
                    ]
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                curl_close($ch);

                return $result;
            }

            $fullMessage = "ชื่อ: $name\nแจ้งปัญหา: $message\nส่งเมื่อ: $dateTime";
            $response = sendBroadcastMessage($fullMessage, $accessToken);

            if ($response) {
                echo "<div class='response'>ข้อความถูกส่งเมื่อ: $dateTime</div>";
            } else {
                echo "<div class='response error'>เกิดข้อผิดพลาด: ไม่สามารถส่งข้อความได้</div>";
            }
        }
        ?>
    </div>
    <script>
        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('focus', () => {
                input.classList.add('animated-bg');
            });
            input.addEventListener('blur', () => {
                input.classList.remove('animated-bg');
            });
        });
    </script>
</body>
</html>
