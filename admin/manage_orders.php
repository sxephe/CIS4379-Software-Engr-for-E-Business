<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

include '../db_connect.php';

$sql = "
    SELECT 
        o.order_id,
        o.total_amount AS total_price,
        o.order_date,
        u.name AS username
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
";
$result = $conn->query($sql);

if (!$result) {
    die('Query error: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Manage Orders</title>
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
          <h2>Order Management</h2>
          <p class="admin-subtitle">View, update, or delete recent orders.</p>
        </div>

        <div class="admin-table-wrapper">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Price</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td>#<?php echo $row['order_id']; ?></td>
                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                  <td>$<?php echo number_format($row['total_price'], 2); ?></td>
                  <td><?php echo $row['order_date']; ?></td>
                  <td class="admin-actions">
                    <a href="view_order.php?order_id=<?php echo $row['order_id']; ?>" class="admin-link">View</a>
                    <a href="update_order_status.php?order_id=<?php echo $row['order_id']; ?>" class="admin-link">Update</a>
                    <a href="delete_order.php?order_id=<?php echo $row['order_id']; ?>"
                       class="admin-link admin-link-danger"
                       onclick="return confirm('Are you sure you want to delete this order?');">
                       Delete
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="admin-empty">No orders found.</td>
              </tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
