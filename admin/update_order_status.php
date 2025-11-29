<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    die("No order ID provided.");
}

$order_id = (int)$_GET['order_id'];

$sql = "SELECT status FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$currentRow = $result->fetch_assoc();

if (!$currentRow) {
    die("Order not found.");
}

$current = $currentRow['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];

    $update = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt_upd = $conn->prepare($update);
    $stmt_upd->bind_param("si", $new_status, $order_id);
    if ($stmt_upd->execute()) {
        header("Location: manage_orders.php");
        exit;
    } else {
        die("Update failed: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Update Order Status</title>
  <link rel="stylesheet" href="adminstyle.css">
</head>
<body class="admin-body">
  <div class="admin-page-wrapper">
    <header class="admin-header">
      <h1>Admin Panel</h1>
      <div class="admin-header-right">
        <span class="admin-user">Logged in as <?php echo htmlspecialchars($_SESSION['admin']); ?></span>
        <a href="admin_logout.php" class="admin-link">Logout</a>
      </div>
    </header>

    <main class="admin-main">
      <div class="admin-card-wide" style="max-width: 520px;">
        <div class="admin-card-header">
          <h2>Update Order Status</h2>
          <p class="admin-subtitle">
            Order #<?php echo $order_id; ?> – current status: <strong><?php echo htmlspecialchars($current); ?></strong>
          </p>
        </div>

        <form method="POST" class="admin-form">
          <div class="form-group">
            <label for="status">New status</label>
            <select name="status" id="status">
              <option value="Pending"   <?php if ($current == 'Pending')   echo 'selected'; ?>>Pending</option>
              <option value="Preparing" <?php if ($current == 'Preparing') echo 'selected'; ?>>Preparing</option>
              <option value="Ready"     <?php if ($current == 'Ready')     echo 'selected'; ?>>Ready</option>
              <option value="Completed" <?php if ($current == 'Completed') echo 'selected'; ?>>Completed</option>
            </select>
          </div>

          <button type="submit" class="admin-btn" style="margin-top:0.8rem;">Save</button>
        </form>

        <p style="margin-top:1rem; font-size:14px;">
          <a href="manage_orders.php" class="admin-link">Back to Orders</a>
        </p>
      </div>
    </main>
  </div>
</body>
</html>
