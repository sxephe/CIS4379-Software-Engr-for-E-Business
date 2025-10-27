<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';
include 'navbar.php';

// Redirect to login if not logged in
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

// Define menu items
$menu_items = [
    "Pepperoni Pizza" => 12.99,
    "Cheeseburger" => 8.99,
    "Caesar Salad" => 6.49,
    "Fountain Drink" => 1.50
];

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal']) && !isset($_POST['remove_item']) && !isset($_POST['clear_cart'])) {
    $meal = htmlspecialchars($_POST['meal']);
    $pickup = htmlspecialchars($_POST['pickup']);
    $_SESSION['cart'][] = [
        'meal' => $meal,
        'pickup' => $pickup,
        'price' => $menu_items[$meal]
    ];
    echo "<p style='color:green; font-weight:bold;'>Added $meal (\$" . number_format($menu_items[$meal], 2) . ") to your cart!</p>";
}

// Clear cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    echo "<p style='color:red; font-weight:bold;'>Cart cleared!</p>";
}

// Remove single item
if (isset($_POST['remove_item'])) {
    $index = intval($_POST['remove_item']);
    if (isset($_SESSION['cart'][$index])) {
        $removed = $_SESSION['cart'][$index]['meal'];
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        echo "<p style='color:orange; font-weight:bold;'>Removed $removed from your cart.</p>";
    }
}
?>

<h1>Your Order</h1>

<form method="POST">
    <label for="meal">Choose a meal:</label>
    <select name="meal" id="meal" required>
        <?php foreach ($menu_items as $meal => $price): ?>
            <option value="<?php echo $meal; ?>"><?php echo $meal . " ($" . number_format($price,2) . ")"; ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="pickup">Pickup time:</label>
    <input type="time" name="pickup" id="pickup" required><br><br>

    <button type="submit">Add to Cart</button>
    <button type="submit" name="clear_cart">Clear Cart</button>
</form>

<h2>Current Cart</h2>
<?php if (!empty($_SESSION['cart'])): ?>
    <ul>
        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
            <li>
                <?php echo htmlspecialchars($item['meal']); ?> - $<?php echo number_format($item['price'],2); ?> (Pickup: <?php echo htmlspecialchars($item['pickup']); ?>)
                <form method="POST" style="display:inline;">
                    <button type="submit" name="remove_item" value="<?php echo $index; ?>">Remove</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="cart.php"><button>Go to Cart / Checkout</button></a>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
