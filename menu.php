<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

  <h1 class="page-title">Campus Menu</h1>

<div class="menu-wrapper">
  <div class="card-slider">
    <div class="card-slide active">
      <div class="card-row">
        <div class="card-info">
          <span class="item-name">Cheeseburger</span>
          <span class="item-price">$8.99</span>
        </div>
        <img src="cheeseburger.webp" alt="Cheeseburger" />
      </div>
      <div class="card-next-hint">Click to see next item</div>
    </div>

    <div class="card-slide">
      <div class="card-row">
        <div class="card-info">
          <span class="item-name">Pepperoni Pizza</span>
          <span class="item-price">$12.99</span>
        </div>
        <img src="pepperonipizza.webp" alt="Pepperoni Pizza" />
      </div>
      <div class="card-next-hint">Click to see next item</div>
    </div>

    <div class="card-slide">
      <div class="card-row">
        <div class="card-info">
          <span class="item-name">Caesar Salad</span>
          <span class="item-price">$6.49</span>
        </div>
        <img src="ceasarsalad.webp" alt="Caesar Salad" />
      </div>
      <div class="card-next-hint">Click to see next item</div>
    </div>

    <div class="card-slide">
      <div class="card-row">
        <div class="card-info">
          <span class="item-name">Fountain Drink</span>
          <span class="item-price">$1.50</span>
        </div>
        <img src="fountaindrink.png" alt="Fountain Drink" />
      </div>
      <div class="card-next-hint">Click to see next item</div>
    </div>
  </div>
</div>

<script>
  const cardSlides = document.querySelectorAll('.card-slide');
  let cardIndex = 0;

  function showCard(index) {
    cardSlides.forEach((c, i) => {
      c.classList.toggle('active', i === index);
    });
  }

  cardSlides.forEach(card => {
    card.addEventListener('click', () => {
      cardIndex = (cardIndex + 1) % cardSlides.length;
      showCard(cardIndex);
    });
  });
  showCard(cardIndex);
</script>

  <footer>
    <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
  </footer>
</body>
</html>
