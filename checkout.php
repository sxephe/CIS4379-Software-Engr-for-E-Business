<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>TAMUCT | Checkout</title>
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
        <h1 class="page-title">Checkout</h1>
        <p class="status-message">Your cart is empty. <a href="order.php">Go back to order page</a></p>
      </div>

      <footer>
        <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
      </footer>
    </body>
    </html>
    <?php
    exit();
}

if (isset($_POST['confirm_order'])) {
    
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
        $total_amount += $item['price'] * $qty;
    }
    
    $user_id    = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, order_date, total_amount)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("isd", $user_id, $order_date, $total_amount);
    $stmt->execute();

    $order_id = $conn->insert_id;

    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, meal_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($_SESSION['cart'] as $item) {
        $meal_id  = (int)$item['meal_id'];
        $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
        $price    = $item['price'];

        $stmt_item->bind_param("iiid", $order_id, $meal_id, $quantity, $price);
        $stmt_item->execute();
    }

    $_SESSION['cart'] = [];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>TAMUCT | Order Confirmed</title>
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
        <h1 class="page-title">Thank you!</h1>

        <div class="menu-section">
          <div class="menu-item">
            <h3>Order confirmed</h3>
            <p class="card-subtitle">
              Your order number is <strong>#<?php echo $order_id; ?></strong>.
            </p>
            <div class="card-note">
              You will receive your meal at your selected pickup time.
            </div>
            <div class="cart-actions" style="margin-top:1.5rem;">
              <a href="order.php" class="checkout-btn">Place Another Order</a>
            </div>
          </div>
        </div>
      </div>

      <footer>
        <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
      </footer>
    </body>
    </html>
    <?php
    exit();
}

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TAMUCT | Checkout</title>
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
    <h1 class="page-title">Checkout</h1>

    <div class="menu-section">
      <div class="menu-item cart-card">
        <h3>Order Summary</h3>
        <p class="card-subtitle">
          Logged in as <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>.
        </p>

        <ul class="cart-list">
          <?php foreach ($_SESSION['cart'] as $item): ?>
            <?php
              $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
              $line_total = $item['price'] * $qty;
              $total += $line_total;
            ?>
            <li class="cart-row">
              <div class="cart-info">
                <span class="cart-name">
                  <?php echo htmlspecialchars($item['meal_name']); ?>
                  <?php if ($qty > 1): ?>
                    (x<?php echo $qty; ?>)
                  <?php endif; ?>
                </span>
                <span class="cart-meta">
                  Pickup: <?php echo htmlspecialchars($item['pickup']); ?>
                </span>
              </div>
              <span class="cart-total">
                $<?php echo number_format($line_total, 2); ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="cart-summary">
          <span>Total:</span>
          <span class="cart-total">$<?php echo number_format($total, 2); ?></span>
        </div>
      </div>

      <div class="menu-item order-card">
        <h3>Confirm your order</h3>
        <p class="card-subtitle">
          Review your order details, then confirm to place your order.
        </p>

        <form method="POST">
          <div class="card-note">
            By confirming, you agree to pick up your order at the selected time.
          </div>
          <div class="cart-actions" style="margin-top:1.2rem;">
            <button type="submit" name="confirm_order" class="checkout-btn" style="width:100%; text-align:center;">
              Confirm Order
            </button>
          </div>
        </form>

        <div class="cart-actions" style="margin-top:0.8rem;">
          <a href="order.php" class="btn-secondary" style="display:inline-block; padding:0.5rem 1rem; text-decoration:none;">
            Back to Order Page
          </a>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
  </footer>
</body>
</html>
