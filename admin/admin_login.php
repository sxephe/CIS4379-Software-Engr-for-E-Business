<?php
session_start();
include '../db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query admin table
    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Check password hash
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: manage_orders.php");
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>

<h2>Admin Login</h2>

<form method="POST" action="">
    <label>Username:</label>
    <input type="text" name="username" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<p style="color:red;"><?php echo $message; ?></p>

</body>
</html>
