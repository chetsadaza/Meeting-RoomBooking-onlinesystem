<?php
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง (เฉพาะ admin)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<script>
            alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้!');
            window.location.href = 'ha.php';
          </script>";
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


// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $location = $_POST['location'];

    $sql = "INSERT INTO rooms (room_name, capacity, location) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $room_name, $capacity, $location);
    $stmt->execute();

    header("Location: editroom.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $location = $_POST['location'];

    $sql = "UPDATE rooms SET room_name = ?, capacity = ?, location = ? WHERE room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $room_name, $capacity, $location, $id);
    $stmt->execute();

    header("Location: editroom.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM rooms WHERE room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: editroom.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting RoomBooking onlinesystem</title>
    <!-- เพิ่ม Bootstrap -->
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
            <h1>จัดการข้อมูลห้อง</h1>
            <a href="home.php" class="btn btn-secondary">กลับหน้า Home</a>
        </div>

        <!-- ฟอร์มเพิ่มข้อมูล -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">เพิ่มข้อมูลห้อง</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="room" class="form-label">ชื่อห้อง:</label>
                        <input type="text" id="room" name="room_name" class="form-control" placeholder="กรอกชื่อห้อง" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">ความจุ:</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" placeholder="กรอกความจุ" min="1" max="100" step="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">ตำแหน่งที่ตั้ง:</label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="กรอกตำแหน่งที่ตั้ง" required>
                    </div>
                    <button type="submit" name="create" class="btn btn-success">บันทึกข้อมูล</button>
                </form>
            </div>
        </div>

        <!-- ตารางแสดงข้อมูล -->
        <?php
        $sql = "SELECT * FROM rooms";
        $result = $conn->query($sql);
        ?>
        <div class="card">
            <div class="card-header bg-info text-white">รายการห้อง</div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>ชื่อห้อง</th>
                            <th>ความจุ</th>
                            <th>ตำแหน่งที่ตั้ง</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['room_id']; ?></td>
                            <td><?php echo $row['room_name']; ?></td>
                            <td><?php echo $row['capacity']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td>
                                <a href="editroom.php?edit=<?php echo $row['room_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="editroom.php?delete=<?php echo $row['room_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือว่าต้องการลบห้องนี้?');">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ฟอร์มแก้ไขข้อมูล -->
        <?php if (isset($_GET['edit'])): ?>
        <?php
        $id = $_GET['edit'];
        $sql = "SELECT * FROM rooms WHERE room_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
        ?>
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">แก้ไขข้อมูลห้อง</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $room['room_id']; ?>">
                    <div class="mb-3">
                        <label for="room" class="form-label">Room Name:</label>
                        <input type="text" id="room" name="room_name" class="form-control" value="<?php echo $room['room_name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" value="<?php echo $room['capacity']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location:</label>
                        <input type="text" id="location" name="location" class="form-control" value="<?php echo $room['location']; ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">อัปเดตข้อมูล</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>
