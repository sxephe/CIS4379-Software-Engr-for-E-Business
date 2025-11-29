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

$stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    header("Location: manage_orders.php");
    exit;
} else {
    die("Delete failed: " . $conn->error);
}
