<?php
// เริ่มต้นเซสชัน
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง (เฉพาะ admin)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ha.php");
    exit();
}

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
// ฟังก์ชัน UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "update") {
    $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_SANITIZE_NUMBER_INT);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    $sql = "UPDATE reservations SET notes = ?, status = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $notes, $status, $reservation_id);

    if ($stmt->execute()) {
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ!'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error . "');</script>";
    }
}

// ฟังก์ชัน DELETE (ลบหลายรายการ)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if (isset($_POST['delete_ids']) && count($_POST['delete_ids']) > 0) {
        $ids_to_delete = implode(",", $_POST['delete_ids']);
        $sql = "DELETE FROM reservations WHERE reservation_id IN ($ids_to_delete)";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('ลบรายการที่เลือกสำเร็จ!');</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('กรุณาเลือกรายการที่ต้องการลบ!');</script>";
    }
}

// ฟังก์ชัน DELETE (ลบเดี่ยว)
if (isset($_GET['delete_id'])) {
    $reservation_id = $_GET['delete_id'];
    $sql = "DELETE FROM reservations WHERE reservation_id=$reservation_id";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// การแบ่งหน้า
$rows_per_page = 10; // จำนวนแถวต่อหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // ตรวจสอบให้ค่าไม่น้อยกว่า 1
$offset = ($page - 1) * $rows_per_page;

// ดึงข้อมูลสำหรับการแสดงผล
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "
    SELECT r.*, 
           u.name AS user_name, 
           ro.room_name 
    FROM reservations r
    LEFT JOIN users u ON r.user_id = u.user_id
    LEFT JOIN rooms ro ON r.room_id = ro.room_id
    WHERE r.reservation_id LIKE '%$search%' 
       OR r.notes LIKE '%$search%' 
       OR r.third_person_name LIKE '%$search%' 
       OR r.status LIKE '%$search%'
    ORDER BY r.reservation_id DESC
    LIMIT $rows_per_page OFFSET $offset";
$result = $conn->query($sql);

// นับจำนวนแถวทั้งหมดสำหรับการสร้างปุ่มหน้า
$total_rows_query = "
    SELECT COUNT(*) AS total 
    FROM reservations r
    LEFT JOIN users u ON r.user_id = u.user_id
    LEFT JOIN rooms ro ON r.room_id = ro.room_id
    WHERE r.reservation_id LIKE '%$search%' 
       OR r.notes LIKE '%$search%' 
       OR r.third_person_name LIKE '%$search%' 
       OR r.status LIKE '%$search%'";
$total_rows_result = $conn->query($total_rows_query);
$total_rows = $total_rows_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $rows_per_page);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting RoomBooking onlinesystem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

   <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">ระบบจัดการ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    
                    <li class="nav-item">
                        <a class="nav-link active" href="editusers.php">จัดการผู้ใช้</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="editroom.php">จัดการข้อมูลห้อง</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="bookadmin.php">จัดการการจอง</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>จัดการการจอง</h1>
            <a href="home.php" class="btn btn-secondary">กลับหน้า Home</a>
        </div>

    <!-- ค้นหา -->
    <form class="d-flex mb-3" method="GET" action="">
        <input type="text" name="search" class="form-control me-2" placeholder="ค้นหาข้อมูล..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">ค้นหา</button>
    </form>

    <!-- ฟอร์มลบหลายรายการ -->
    <form method="POST" action="">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><input type="checkbox" id="select_all"></th>
                <th>ID</th>
                <th>User Name</th>
                <th>Room Name</th>
                <th>วันที่จอง</th>
                <th>เวลาเริ่ม</th>
                <th>เวลาสิ้นสุด</th>
                <th>หมายเหตุ</th>
                <th>ชื่อบุคคลที่สาม</th>
                <th>สถานะ</th>
                <th>การจัดการ</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="delete_ids[]" value="<?= $row['reservation_id'] ?>" class="select_item"></td>
                        <td><?= htmlspecialchars($row['reservation_id']) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                        <td><?= htmlspecialchars($row['reservation_date']) ?></td>
                        <td><?= htmlspecialchars($row['start_time']) ?></td>
                        <td><?= htmlspecialchars($row['end_time']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                        <td><?= htmlspecialchars($row['third_person_name']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="?edit_id=<?= $row['reservation_id'] ?>" class="btn btn-warning btn-sm">ยืนยัน</a>
                            <a href="?delete_id=<?= $row['reservation_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่?')">ลบ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">ไม่พบข้อมูล</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <button type="submit" name="delete" class="btn btn-danger">ลบที่เลือก</button>
    </form>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Prev</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
            </li>
        </ul>
    </nav>

    <!-- ฟอร์มแก้ไข -->
    <?php if (isset($_GET['edit_id'])): ?>
        <?php
        $edit_id = $_GET['edit_id'];
        $edit_sql = "
            SELECT r.*, 
                   u.name AS user_name, 
                   ro.room_name 
            FROM reservations r
            LEFT JOIN users u ON r.user_id = u.user_id
            LEFT JOIN rooms ro ON r.room_id = ro.room_id
            WHERE r.reservation_id = ?";
        $stmt = $conn->prepare($edit_sql);
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $edit_data = $stmt->get_result()->fetch_assoc();
        ?>
        <h2>ข้อมูลการจอง</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($edit_data['reservation_id']) ?>">

            <div class="mb-3">
                <label for="user_name">ชื่อผู้ใช้</label>
                <input type="text" id="user_name" class="form-control" value="<?= htmlspecialchars($edit_data['user_name']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="room_name">ชื่อห้อง</label>
                <input type="text" id="room_name" class="form-control" value="<?= htmlspecialchars($edit_data['room_name']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="notes">หมายเหตุ</label>
                <input type="text" name="notes" class="form-control" value="<?= htmlspecialchars($edit_data['notes']) ?>">
            </div>

            <div class="mb-3">
                <label for="status">สถานะ</label>
                <select name="status" class="form-control">
                    <option value="รอการอนุมัติ" <?= $edit_data['status'] == "รอการอนุมัติ" ? "selected" : "" ?>>รอการอนุมัติ</option>
                    <option value="อนุมัติ" <?= $edit_data['status'] == "อนุมัติ" ? "selected" : "" ?>>อนุมัติ</option>
                    <option value="ปฏิเสธ" <?= $edit_data['status'] == "ปฏิเสธ" ? "selected" : "" ?>>ปฏิเสธ</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">อัปเดต</button>
        </form>
    <?php endif; ?>
</div>

<script>
    document.getElementById('select_all').addEventListener('click', function (e) {
        const checkboxes = document.querySelectorAll('.select_item');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });
</script>
</body>
</html>
