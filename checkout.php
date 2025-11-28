<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';

// Redirect to login if not logged in
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Display logged-in user
echo "<p>Logged in as <strong>" . htmlspecialchars($_SESSION['name']) . "</strong>! <a href='logout.php'>Logout</a></p>";

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='order.php'>Go back to order page</a></p>";
    include 'footer.php';
    exit();
}

// Handle order confirmation
if (isset($_POST['confirm_order'])) {
    $_SESSION['cart'] = [];
    echo "<p style='color:green; font-weight:bold;'>Thank you! Your order has been confirmed.</p>";
    echo "<p><a href='order.php'>Place another order</a></p>";
    include 'footer.php';
    exit();
}

// Display cart for review
$total = 0;
?>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Meal</th>
        <th>Price</th>
        <th>Pickup Time</th>
    </tr>
    <?php foreach ($_SESSION['cart'] as $item):
        $total += $item['price'];
    ?>
    <tr>
        <td><?php echo htmlspecialchars($item['meal']); ?></td>
        <td>$<?php echo number_format($item['price'], 2); ?></td>
        <td><?php echo htmlspecialchars($item['pickup']); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td style="text-align:right;" colspan="2"><strong>Total:</strong></td>
        <td>$<?php echo number_format($total, 2); ?></td>
    </tr>
</table>

<form method="post" action="checkout.php" style="margin-top:10px;">
    <input type="submit" name="confirm_order" value="Confirm Order">
</form>

<?php include 'footer.php'; ?>
