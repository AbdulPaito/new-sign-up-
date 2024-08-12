<?php
session_start();
include 'database.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Raw password
    $email = $_POST['email'];

    // Basic validation
    if (empty($username) || empty($password) || empty($email)) {
        echo "All fields are required.";
        exit;
    }

    // Check if username is already taken
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Output detailed error message
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists.";
    } else {
        // Insert new user into users table with raw password
        $insert_sql = "INSERT INTO users (username, password, email, status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($insert_sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error); // Output detailed error message
        }
        $stmt->bind_param("sss", $username, $password, $email);

        if ($stmt->execute()) {
            // Automatically log the user in after successful registration
            $_SESSION['user_id'] = $conn->insert_id; // Store the new user's ID in session
            $_SESSION['username'] = $username; // Store username in session

            echo "User registered and logged in successfully.";
            // Redirect to page1.php after successful registration
            header('Location: page1.php');
            exit;
        } else {
            echo "Error registering user: " . $stmt->error;
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
  <link rel="stylesheet" href="log.css">
</head>
<body>
  <section>
    <div class="signin">
      <div class="content">
        <h2>Sign Up</h2>
        <form class="form" action="signup.php" method="POST">
        <div class="inputBox">
          <input type="text" name="username" required>
          <label>Username</label>
        </div>
        <div class="inputBox">
          <input type="password" name="password" required>
          <label>Password</label>
        </div>
        <div class="inputBox">
          <input type="email" name="email" required>
          <label>Email</label>
        </div>
        <div class="inputBox">
          <input type="submit" value="Sign Up">
        </div>
      </form>

      </div>
    </div>
  </section>
</body>
</html>
