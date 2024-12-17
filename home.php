<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // ส่งกลับไปหน้าล็อกอิน
    exit();
}

if (isset($_GET['navbar__link'])) {
  session_destroy();
  unset($_SESSIN['user_id']);
  header('location: login.html');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meeting RoomBooking onlinesystem</title>
  <link rel="stylesheet" href="./css/styles.css"> <!-- ลิงก์ไปยังไฟล์ CSS -->
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js" defer></script>
  <script src="./js/settings.js" defer></script> <!-- ลิงก์ไปยังไฟล์ JS -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      feather.replace(); // ใช้ Feather Icons
    });
  </script>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar">
    <ul class="navbar__menu">
      <!-- calendar -->
      <li class="navbar__item" id="homeMenu">
        <a href="book.php" class="navbar__link">
          <i data-feather="calendar"></i>
        </a>
        <span class="navbar__tooltip">จองห้องประชุม</span>
      </li>

      <!-- Customers -->
      <li class="navbar__item">
        <a href="ha.php" class="navbar__link">
          <i data-feather="users"></i>
        </a>
        <span class="navbar__tooltip">ข้อมูลส่วนตัว</span>
      </li>

      <!-- Projects -->
      <li class="navbar__item">
        <a href="graph.php" class="navbar__link">
          <i data-feather="folder"></i>
        </a>
        <span class="navbar__tooltip">กราฟสถิติการจองรายเดือน</span>
      </li>

<!-- Resources -->
<li class="navbar__item" id="resourcesMenu">
  <a href="#" class="navbar__link">
    <i data-feather="archive"></i>
  </a>
  <span class="navbar__tooltip">ระบบจัดการ(ของผู้ดูแล)</span>

  <!-- Submenu -->
  <ul class="navbar__submenu">
    <!-- Manage Rooms -->
    <li class="navbar__subitem">
      <a href="editroom.php" class="navbar__sublink">
        <i data-feather="map"></i>
        <span>จัดการห้อง</span>
      </a>
    </li>
    <!-- Manage Users -->
    <li class="navbar__subitem">
      <a href="editusers.php" class="navbar__sublink">
        <i data-feather="user"></i>
        <span>จัดการข้อมูลผู้ใช้</span>
      </a>
    </li>
    <!-- Manage admin -->
    <li class="navbar__subitem">
      <a href="bookadmin.php" class="navbar__sublink">
        <i data-feather="calendar"></i>
        <span>จัดการจองห้องประชุม</span>
      </a>
    </li>
  </ul>
</li>

      <!-- Log-in -->
<li class="navbar__item">
  <a href="logout.php" class="navbar__link">
    <i data-feather="log-out"></i>
  </a>
  <span class="navbar__tooltip">log-out</span>
</li>

      <!-- Settings -->
<li class="navbar__item" id="resourcesMenu">
  <a href="#" class="navbar__link">
    <i data-feather="settings"></i>
  </a>
  <span class="navbar__tooltip">ระบบจัดการ(ของผู้ดูแล)</span>

  <!-- Submenu -->
  <ul class="navbar__submenu">
    <!-- Manage Rooms -->
    <li class="navbar__subitem">
      <a href="line_notify.php" class="navbar__sublink">
        <i data-feather="message-square"></i>
        <span>แจ้งปัญหา</span>
      </a>
    </li>
        </ul>
      </li>
    </ul>
  </nav>

</body>
</html>
