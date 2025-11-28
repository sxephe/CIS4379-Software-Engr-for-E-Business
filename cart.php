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
?>

<h1>Your Cart</h1>

<?php if (!empty($_SESSION['cart'])): ?>
    <ul>
        <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $item):
            $total += $item['price'];
        ?>
        <li>
            <?php echo htmlspecialchars($item['meal']); ?> - $<?php echo number_format($item['price'], 2); ?>
            (Pickup: <?php echo htmlspecialchars($item['pickup']); ?>)
        </li>
        <?php endforeach; ?>
    </ul>

    <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>

    <form action="checkout.php" method="POST">
        <button type="submit">Proceed to Checkout</button>
    </form>

<?php else: ?>
    <p>Your cart is currently empty.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
