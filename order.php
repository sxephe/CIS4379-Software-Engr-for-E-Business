<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

$meals = [];
$sql = "SELECT meal_id, meal_name, price FROM meals WHERE status = 'available'";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $meals[] = $row;
    }
} else {
    die("Error loading meals: " . $conn->error);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$status_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id']) && !isset($_POST['remove_item']) && !isset($_POST['clear_cart'])) {
    $meal_id = (int)$_POST['meal_id'];
    $pickup  = htmlspecialchars($_POST['pickup']);

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
            'quantity'  => 1
        ];
        $status_message = "Added " . htmlspecialchars($selected_meal['meal_name']) .
                          " ($" . number_format($selected_meal['price'], 2) . ") to your cart!";
    } else {
        $status_message = "Selected meal not found.";
    }
}

if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    $status_message = "Cart cleared!";
}

if (isset($_POST['remove_item'])) {
    $index = intval($_POST['remove_item']);
    if (isset($_SESSION['cart'][$index])) {
        $removed = $_SESSION['cart'][$index]['meal_name'];
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $status_message = "Removed " . htmlspecialchars($removed) . " from your cart.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TAMUCT | Online Food Ordering</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="welcome-bar">
    <?php if (isset($_SESSION['name'])): ?>
      <span>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
    <?php endif; ?>
  </div>

  <header>
    <div class="logo">
      <img src="Logo-Dark.svg" alt="TAMUCT Logo" />
    </div>
    <?php include 'navbar.php'; ?>
  </header>

  <div class="menu-wrapper">
    <h1 class="page-title">Your Order</h1>

    <?php if (!empty($status_message)): ?>
      <p class="status-message"><?php echo $status_message; ?></p>
    <?php endif; ?>

    <div class="menu-section">
      <div class="menu-item order-card">
        <h3>Select Your Meal</h3>
        <p class="card-subtitle">Choose a meal and pickup time, then add it to your cart.</p>

        <form method="POST" class="order-form">
          <div class="form-group">
            <label for="meal">Meal</label>
            <select name="meal_id" id="meal" required>
              <?php foreach ($meals as $meal): ?>
                <option value="<?php echo $meal['meal_id']; ?>">
                  <?php echo htmlspecialchars($meal['meal_name']) . " ($" . number_format($meal['price'], 2) . ")"; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="pickup">Pickup Time</label>
            <input type="time" name="pickup" id="pickup" required>
          </div>

          <div class="button-row">
            <button type="submit">Add to Cart</button>
            <button type="submit" name="clear_cart" class="btn-secondary">Clear Cart</button>
          </div>
        </form>

        <div class="card-note">
          Same‑day orders are prepared fresh at your selected pickup time.
        </div>
      </div>

      <div class="menu-item cart-card">
        <h3>Your Cart</h3>

        <?php if (!empty($_SESSION['cart'])): ?>
          <ul class="cart-list">
            <?php
              $total = 0;
              foreach ($_SESSION['cart'] as $index => $item):
                $total += $item['price'] * $item['quantity'];
            ?>
              <li class="cart-row">
                <div class="cart-info">
                  <span class="cart-name"><?php echo htmlspecialchars($item['meal_name']); ?></span>
                  <span class="cart-meta">
                    Pickup: <?php echo htmlspecialchars($item['pickup']); ?> ·
                    $<?php echo number_format($item['price'], 2); ?>
                  </span>
                </div>
                <form method="POST" class="cart-remove-form">
                  <button type="submit" name="remove_item" value="<?php echo $index; ?>" class="btn-link">
                    Remove
                  </button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>

          <div class="cart-summary">
            <span>Total:</span>
            <span class="cart-total">$<?php echo number_format($total, 2); ?></span>
          </div>

          <div class="cart-actions">
            <a href="checkout.php" class="checkout-btn">Go to Checkout</a>
          </div>
        <?php else: ?>
          <p class="empty-cart">Your cart is empty. Add a meal to get started.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
  </footer>
</body>
</html>
