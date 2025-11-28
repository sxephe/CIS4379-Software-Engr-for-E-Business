<?php
session_start();
include 'db_connect.php';
include 'navbar.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Make sure cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='order.php'>Go back to order page</a></p>";
    include 'footer.php';
    exit();
}

// Handle order confirmation (when user clicks confirm)
if (isset($_POST['confirm_order'])) {

    // 1) Calculate total_amount
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        // Your cart currently has: meal, pickup, price [attached_file:44]
        // If you later add quantity, multiply price * quantity here.
        $total_amount += $item['price'];
    }

    // 2) Insert into orders table
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, order_date, total_amount)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("isd", $user_id, $order_date, $total_amount);
    $stmt->execute();

    // Get new order_id
    $order_id = $conn->insert_id;

    // 3) Insert each cart item into order_items
    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, meal_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($_SESSION['cart'] as $item) {
        // For now, there is no meal_id or quantity in your cart,
        // only meal name and price. [attached_file:44]
        // To fully use order_items, you need to store meal_id (from meals table)
        // and quantity in the cart. For now, use quantity = 1 and meal_id = 0 as placeholder.
        $meal_id = 0;          // TODO: replace with real meal_id once you connect to meals table
        $quantity = 1;
        $price = $item['price'];

        $stmt_item->bind_param("iiid", $order_id, $meal_id, $quantity, $price);
        $stmt_item->execute();
    }

    // 4) Clear cart
    $_SESSION['cart'] = [];

    echo "<h2>Thank you! Your order has been confirmed.</h2>";
    echo "<p>Your order number is #{$order_id}.</p>";
    echo "<p><a href='order.php'>Place another order</a></p>";
    include 'footer.php';
    exit();
}

// If not yet confirmed, show review page
$total = 0;
?>

<h2>Checkout</h2>
<p>Logged in as <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>! <a href="logout.php">Logout</a></p>

<table border="1" cellpadding="10">
    <tr>
        <th>Meal</th>
        <th>Price</th>
        <th>Pickup Time</th>
    </tr>
    <?php foreach ($_SESSION['cart'] as $item): ?>
        <?php $total += $item['price']; ?>
        <tr>
            <td><?php echo htmlspecialchars($item['meal']); ?></td>
            <td>$<?php echo number_format($item['price'], 2); ?></td>
            <td><?php echo htmlspecialchars($item['pickup']); ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td><strong>Total:</strong></td>
        <td colspan="2">$<?php echo number_format($total, 2); ?></td>
    </tr>
</table>

<form method="POST">
    <button type="submit" name="confirm_order">Confirm Order</button>
</form>

<?php include 'footer.php'; ?>
