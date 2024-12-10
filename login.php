<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'email_not_found'): ?>
                <div class="error">Email not found.</div>
            <?php endif; ?>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
                <div class="error">Invalid password.</div>
            <?php endif; ?>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>


<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        //This will verify the email and password only registered users can login
        if (password_verify($password, $hashed_password)) {
            header("Location: welcome.php");
            exit;
        } else {
            header("Location: login.php?error=invalid_credentials&email=" . urlencode($email));
            exit;
        }
    } else {
        header("Location: login.php?error=email_not_found&email=" . urlencode($email));
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>
