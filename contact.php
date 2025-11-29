<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TAMUCT | Contact Us</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="welcome-bar">
    <?php if (isset($_SESSION['name'])): ?>
      <span>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
    <?php endif; ?>
  </div>

  <header>
    <div class="logo">
      <img src="Logo-Dark.svg" alt="TAMUCT Logo" />
    </div>
    <?php include 'navbar.php'; ?>
  </header>

  <div class="menu-wrapper">
    <h1 class="page-title">Contact Us</h1>

    <div class="menu-section">
      <div class="menu-item">
        <h3>Get In Touch</h3>
        <p class="card-subtitle">
          Reach out to the campus dining team with any questions about your order.
        </p>

        <p><strong>Email:</strong> support@campusmeals.com</p>
        <p><strong>Phone:</strong> (254) 123-4567</p>

        <div class="card-note">
          Support hours: Monday–Friday, 9:00 AM – 5:00 PM (CST).
        </div>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
  </footer>
</body>
</html>
