<?php
// Start session only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require admin login
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

include '../db_connect.php';

// Query using your actual column names
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

// Show error if query fails (for debugging)
if (!$result) {
    die('Query error: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
</head>
<body>

<h2>Admin – Order Management</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Total Price</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td>$<?php echo number_format($row['total_price'], 2); ?></td>
            <td><?php echo $row['order_date']; ?></td>
            <td>
                <a href="view_order.php?order_id=<?php echo $row['order_id']; ?>">View</a> |
                <a href="update_order_status.php?order_id=<?php echo $row['order_id']; ?>">Update</a> |
                <a href="delete_order.php?order_id=<?php echo $row['order_id']; ?>"
                   onclick="return confirm('Are you sure you want to delete this order?');">
                   Delete
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No orders found.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
