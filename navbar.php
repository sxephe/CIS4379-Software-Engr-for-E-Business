<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="main-nav">
    <a href="menu.php">MENU</a>
    <a href="order.php">ORDER</a>
    <a href="cart.php">CART</a>
    <a href="contact.php">CONTACT</a>

    <?php if (isset($_SESSION['name'])): ?>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
        <a href="logout.php">LOGOUT</a>
    <?php else: ?>
        <a href="login.php">LOGIN</a>
        <a href="signup.php">SIGN UP</a>
    <?php endif; ?>
</nav>
