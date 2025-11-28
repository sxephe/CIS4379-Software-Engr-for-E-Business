<?php
include 'db_connect.php';
session_start();

$error='';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name']; // <-- consistent with navbar and order.php
            header("Location: menu.php");
            exit();
        } else {
            $error = "Invalid Password";
        }
    } else {
        $error = "User Not Found";
    }
}
?>
    
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TAMUCT | Login </title>
  <link rel="stylesheet" href="loginstyle.css">
</head>

<body>
  <header>
    <img src="Logo-Dark.svg" alt="TAMUCT Logo" />
  </header>

  <div class="login-container">
    <form action="" method="POST">
      <h2>Login</h2>
      
      <?php if ($error): ?>
      <p class="error"><?php echo $error; ?></p>
      <?php endif; ?>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit" class="btn-submit">Login</button>
      <p class="signup-link">Don’t have an account? <a href="signup.php">Sign up here</a></p>
    </form>
  </div>

  <footer>
    <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
  </footer>
</body>
</html>

