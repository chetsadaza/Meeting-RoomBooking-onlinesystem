<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meeting RoomBooking onlinesystem</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #e3ffe7, #d9e7ff);
      color: #333;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
      background-color: #3b3b98;
      color: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar__menu {
      list-style: none;
      display: flex;
      gap: 20px;
    }

    .navbar__item {
      position: relative;
    }

    .navbar__link {
      text-decoration: none;
      color: white;
      font-size: 1.2em;
      transition: color 0.3s;
    }

    .navbar__link:hover {
      color: #ffd700;
    }

    .navbar__submenu {
      position: absolute;
      top: 100%;
      left: 0;
      background-color: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 5px;
      overflow: hidden;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s;
    }

    .navbar__item:hover .navbar__submenu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .navbar__submenu li {
      list-style: none;
    }

    .navbar__sublink {
      text-decoration: none;
      display: block;
      padding: 10px 20px;
      color: #333;
      transition: background 0.3s;
    }

    .navbar__sublink:hover {
      background-color: #f0f0f0;
    }

    .hero {
      text-align: center;
      padding: 100px 20px;
      background: url('https://source.unsplash.com/1200x600/?nature,technology') no-repeat center/cover;
      color: #9999FF;
      box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .hero h1 {
      font-size: 3em;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 1.2em;
    }

    .section {
      padding: 40px 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .section h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 2em;
      color: #3b3b98;
    }

    .section iframe {
      width: 100%;
      height: 500px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    footer {
      text-align: center;
      padding: 20px 0;
      background-color: #3b3b98;
      color: white;
      margin-top: 40px;
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="logo"></div>
    <ul class="navbar__menu">
      <li class="navbar__item"><a href="#" class="navbar__link"><i class="fas fa-home"></i> Home</a></li>
      <li class="navbar__item"><a href="login.html" class="navbar__link"><i class="fas fa-sign-in-alt"></i> Login</a></li>
      <li class="navbar__item">
        <a href="#" class="navbar__link"><i class="fas fa-cogs"></i> Settings</a>
        <ul class="navbar__submenu">
          <li><a href="#" class="navbar__sublink">Messages</a></li>
          <li><a href="#" class="navbar__sublink">Help</a></li>
        </ul>
      </li>
    </ul>
  </nav>

  <div class="hero">
    <h1>ยินดีต้อนรับเข้าสู่เว็บMeeting RoomBooking onlinesystem</h1>
    <p>ท่านสามารถตรวจสอบวันและเวลาที่จะจองก่อนเข้าสู่หน้าloginเพื่อจอง</p>
  </div>

  <section class="section">
    <h2>Reservation Calendar</h2>
    <iframe src="view_calendar.php"></iframe>
  </section>

  <footer>
    <p>&copy; 2024 Redesigned by Chsada💝 All rights reserved.</p>
  </footer>
</body>
</html>
