<?php
// เชื่อมต่อฐานข้อมูล
$servername = "sql207.infinityfree.com";  // MySQL Hostname
$username = "if0_37934260";               // MySQL Username
$password = "04249910zaZA";               // MySQL Password
$dbname = "if0_37934260_db_t";            // Database Name
$port = 3306;                             // MySQL Port

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Query ข้อมูลการจองแยกตามชื่อห้องและเดือน
$query = "SELECT MONTH(r.reservation_date) AS month, rm.room_name, COUNT(*) AS total_reservations 
          FROM reservations r
          INNER JOIN rooms rm ON r.room_id = rm.room_id
          GROUP BY MONTH(r.reservation_date), rm.room_name";

$result = $conn->query($query);

// เตรียมข้อมูลสำหรับ Chart.js
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting RoomBooking onlinesystem</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* จัดการหน้าจอให้สมดุล */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        h1 {
            margin: 20px 0;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 70vh; /* ปรับความสูงของกราฟ */
            width: 90vw;  /* ปรับความกว้างของกราฟ */
        }
    </style>
</head>
<body>
    <h1>กราฟสถิติการจองแยกตามชื่อห้องรายเดือน</h1>
    <div class="chart-container">
        <canvas id="reservationChart"></canvas>
    </div>

    <script>
        // รับข้อมูลจาก PHP
        const dataFromPHP = <?php echo json_encode($data); ?>;

        // ชื่อเดือนภาษาไทย
        const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                            "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];

        // จัดกลุ่มข้อมูลตามห้องและเดือน
        const rooms = [...new Set(dataFromPHP.map(item => item.room_name))];
        const months = [...new Set(dataFromPHP.map(item => item.month))];

        const datasets = rooms.map(room => {
            return {
                label: room,
                data: months.map(month => {
                    const entry = dataFromPHP.find(item => item.room_name === room && item.month == month);
                    return entry ? entry.total_reservations : 0;
                }),
                backgroundColor: `rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 0.7)`,
                borderColor: 'rgba(0, 0, 0, 0.8)',
                borderWidth: 1
            };
        });

        const labels = months.map(month => monthNames[month - 1]);

        // สร้างกราฟ Chart.js
        const ctx = document.getElementById('reservationChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, /* ปรับให้ยืดหยุ่นตามพื้นที่ */
                plugins: {
                    title: {
                        display: true,
                        text: 'สถิติการจองแยกตามชื่อห้องรายเดือน'
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'จำนวนการจอง'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'เดือน'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
