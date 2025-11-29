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

$order_sql = "
    SELECT o.order_id,
           o.order_date,
           o.total_amount,
           o.status,
           u.name AS customer_name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ?
";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$item_sql = "
    SELECT oi.quantity,
           oi.price,
           oi.meal_id
    FROM order_items oi
    WHERE oi.order_id = ?
";
$stmt_items = $conn->prepare($item_sql);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Order #<?php echo $order_id; ?> Details</title>
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
      <div class="admin-card-wide">
        <div class="admin-card-header">
          <h2>Order #<?php echo $order_id; ?></h2>
          <p class="admin-subtitle">
            Customer: <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong> ·
            Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong>
          </p>
        </div>

        <div style="margin-bottom: 1rem; font-size:14px; color:#555;">
          <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
          <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
        </div>

        <h3 style="margin-top:0.5rem; font-size:18px;">Items</h3>

        <div class="admin-table-wrapper">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Meal ID</th>
                <th>Quantity</th>
                <th>Price (each)</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($items->num_rows > 0): ?>
              <?php while ($row = $items->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $row['meal_id']; ?></td>
                  <td><?php echo $row['quantity']; ?></td>
                  <td>$<?php echo number_format($row['price'], 2); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="admin-empty">No items found for this order.</td>
              </tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        <p style="margin-top:1rem; font-size:14px;">
          <a href="manage_orders.php" class="admin-link">Back to Orders</a>
        </p>
      </div>
    </main>
  </div>
</body>
</html>
