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

// Get current status
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

// Handle status update
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
<html>
<head>
    <title>Update Order Status</title>
</head>
<body>

<h2>Update Order Status (Order #<?php echo $order_id; ?>)</h2>

<form method="POST">
    <select name="status">
        <option value="Pending"   <?php if ($current == 'Pending')   echo 'selected'; ?>>Pending</option>
        <option value="Preparing" <?php if ($current == 'Preparing') echo 'selected'; ?>>Preparing</option>
        <option value="Ready"     <?php if ($current == 'Ready')     echo 'selected'; ?>>Ready</option>
        <option value="Completed" <?php if ($current == 'Completed') echo 'selected'; ?>>Completed</option>
    </select>
    <button type="submit">Save</button>
</form>

<p><a href="manage_orders.php">Back to Orders</a></p>

</body>
</html>
