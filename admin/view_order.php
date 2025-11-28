<?php
session_start();
include '../db_connect.php';

// Require admin login
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    die("No order ID provided.");
}

$order_id = (int)$_GET['order_id'];

// Get order + user
$order_sql = "
    SELECT o.order_id,
           o.order_date,
           o.total_amount,
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

// Get items for this order
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
<html>
<head>
    <title>Order #<?php echo $order_id; ?> Details</title>
</head>
<body>

<h2>Order #<?php echo $order_id; ?></h2>
<p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
<p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
<p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>

<h3>Items</h3>
<table border="1" cellpadding="10">
    <tr>
        <th>Meal ID</th>
        <th>Quantity</th>
        <th>Price</th>
    </tr>
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
            <td colspan="3">No items found for this order.</td>
        </tr>
    <?php endif; ?>
</table>

<p><a href="manage_orders.php">Back to Orders</a></p>

</body>
</html>
