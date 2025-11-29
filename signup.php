<?php
include 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password_plain   = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password_plain !== $confirm_password) {
        $error = "Passwords Do Not Match.";
    } else {
        $password = password_hash($password_plain, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "This email is already registered. Try logging in.";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        
        if ($stmt->execute()) {
            $success = "Account created successfully! You can now log in.";
        } else {
            $error = "Error creating account. Please try again later.";
        }
     }
   }
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="loginstyle.css">
</head>
<body>
  <header>
    <img src="Logo-Dark.svg" alt="TAMUCT Logo" />
  </header>

  <div class="signup-container">
    <form action="" method="POST">
      <h2>Create an Account</h2> 

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <label for="fullname">Full Name</label>
    <input type="text" id="name" name="name" placeholder="Your full name" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="Your email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Enter password" required>

    <label for="confirm-password">Confirm Password</label>
    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm password" required>

    <button type="submit" class="btn-submit">Sign Up</button>
    <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
  </div>

  <footer>
    <p>&copy; 2025 TAMUCT Online Food Ordering. All Rights Reserved.</p>
  </footer>
</body>
</html>
