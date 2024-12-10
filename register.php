<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name">
            <?php if (isset($_GET['error']) && $_GET['error'] === 'name_required'): ?>
                <div class="error">Full Name is required.</div>
            <?php endif; ?>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <?php if (isset($_GET['error']) && $_GET['error'] === 'email_invalid'): ?>
                <div class="error">Invalid email format.</div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'email_registered'): ?>
                <div class="error">Email is already registered.</div>
            <?php endif; ?>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" minlength="6">
            <?php if (isset($_GET['error']) && $_GET['error'] === 'password_short'): ?>
                <div class="error">Password must be at least 6 characters.</div>
            <?php endif; ?>
            
            <button type="submit"id="login-button">Register</button>
        </form>
    </div>
</body>
</html>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

   //Name validation
    if (empty($full_name)) {
        header("Location: register.php?error=name_required&email=" . urlencode($email));
        exit;
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=email_invalid&full_name=" . urlencode($full_name));
        exit;
    }

    // Password validation
    if (strlen($password) < 6) {
        header("Location: register.php?error=password_short&full_name=" . urlencode($full_name) . "&email=" . urlencode($email));
        exit;
    }

    // Check if email is already registered in the DB
    $check_email_query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: register.php?error=email_registered&full_name=" . urlencode($full_name));
        exit;
    }

    $stmt->close();

    //Password get hasehed with this method
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sss", $full_name, $email, $hashed_password);

    if ($stmt->execute()) {
        // Redirect to success page
        header("Location: success.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

