<?php
session_start();
include '../db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: manage_orders.php");
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Admin not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="adminstyle.css">
</head>
<body>
  <div class="admin-wrapper">
    <div class="admin-card">
      <h2>Admin Login</h2>
      <p class="admin-subtitle">Sign in to manage orders and meals.</p>

      <?php if (!empty($message)): ?>
        <p class="admin-message"><?php echo htmlspecialchars($message); ?></p>
      <?php endif; ?>

      <form method="POST" class="admin-form" action="">
        <div class="form-group">
          <label for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            required
            autocomplete="username"
          >
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password"
          >
        </div>

        <button type="submit" class="admin-btn">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
