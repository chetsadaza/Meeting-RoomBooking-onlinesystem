<?php
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง (เฉพาะ admin)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<script>
            alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้!');
            window.location.href = 'home.php';
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


// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($role)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('รูปแบบอีเมลไม่ถูกต้อง');</script>";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo "<script>alert('เบอร์โทรต้องมี 10 หลัก');</script>";
    } else {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $role, $id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('แก้ไขข้อมูลสำเร็จ');
                    window.location.href = 'editusers.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล');</script>";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>
            alert('ลบข้อมูลสำเร็จ');
            window.location.href = 'editusers.php';
          </script>";
    exit();
}

// Handle Search
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
}
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
    <!-- Main Content -->
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>จัดการข้อมูลผู้ใช้</h1>
            <a href="home.php" class="btn btn-secondary">กลับหน้า Home</a>
        </div>

        <!-- ช่องค้นหา -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาโดยชื่อหรืออีเมล..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </div>
        </form>

        <!-- ตารางแสดงข้อมูล -->
        <div class="card">
            <div class="card-header bg-info text-white">รายการผู้ใช้</div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทร</th>
                            <th>บทบาท</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['role']; ?></td>
                                <td>
                                    <a href="editusers.php?edit=<?php echo $row['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="editusers.php?delete=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือว่าต้องการลบผู้ใช้นี้?');">Delete</a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">ไม่พบข้อมูล</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ฟอร์มแก้ไขข้อมูล -->
        <?php if (isset($_GET['edit'])): ?>
        <?php
        $id = $_GET['edit'];
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        ?>
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">แก้ไขข้อมูลผู้ใช้</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $user['user_id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อ:</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">เบอร์โทร:</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">บทบาท:</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">บันทึก</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
