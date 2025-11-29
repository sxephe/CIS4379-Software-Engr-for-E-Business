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

// Load meals from the meals table
$meals = [];
$sql = "SELECT meal_id, meal_name, price FROM meals WHERE status = 'available'";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $meals[] = $row; // each row has meal_id, meal_name, price
    }
} else {
    die("Error loading meals: " . $conn->error);
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id']) && !isset($_POST['remove_item']) && !isset($_POST['clear_cart'])) {
    $meal_id = (int)$_POST['meal_id'];
    $pickup  = htmlspecialchars($_POST['pickup']);

    // Find the selected meal in the $meals array
    $selected_meal = null;
    foreach ($meals as $meal) {
        if ((int)$meal['meal_id'] === $meal_id) {
            $selected_meal = $meal;
            break;
        }
    }

    if ($selected_meal) {
        $_SESSION['cart'][] = [
            'meal_id'   => $selected_meal['meal_id'],
            'meal_name' => $selected_meal['meal_name'],
            'pickup'    => $pickup,
            'price'     => (float)$selected_meal['price'],
            'quantity'  => 1 // default quantity = 1 for now
        ];
        echo "<p style='color:green; font-weight:bold;'>Added " . htmlspecialchars($selected_meal['meal_name']) .
             " ($" . number_format($selected_meal['price'], 2) . ") to your cart!</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>Selected meal not found.</p>";
    }
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
        $removed = $_SESSION['cart'][$index]['meal_name'];
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        echo "<p style='color:orange; font-weight:bold;'>Removed " . htmlspecialchars($removed) . " from your cart.</p>";
    }
}
?>

<h1>Your Order</h1>

<form method="POST">
    <label for="meal">Choose a meal:</label>
    <select name="meal_id" id="meal" required>
        <?php foreach ($meals as $meal): ?>
            <option value="<?php echo $meal['meal_id']; ?>">
                <?php echo htmlspecialchars($meal['meal_name']) . " ($" . number_format($meal['price'], 2) . ")"; ?>
            </option>
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
                <?php echo htmlspecialchars($item['meal_name']); ?>
                - $<?php echo number_format($item['price'], 2); ?>
                (Pickup: <?php echo htmlspecialchars($item['pickup']); ?>)
                <form method="POST" style="display:inline;">
                    <button type="submit" name="remove_item" value="<?php echo $index; ?>">Remove</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="checkout.php"><button type="button">Go to Checkout</button></a>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
