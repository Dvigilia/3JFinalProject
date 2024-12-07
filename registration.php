<?php
// Include your database connection file
include 'Bdatabase.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Collect the form data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $password = $_POST['password'];
    $role = 'customer';  // Default role set as 'customer'

    // Check if the email already exists in the database
    $check_email_query = "SELECT * FROM Users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email_query);
    
    if (mysqli_num_rows($result) > 0) {
        $errorMessage = "Email already exists. Please use a different email.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO Users (full_name, email, phone_number, password, role) 
                VALUES ('$full_name', '$email', '$phone_number', '$hashed_password', '$role')";

        if (mysqli_query($conn, $sql)) {
            // Redirect to login page after successful registration
            header("Location: login.php");  
            exit();
        } else {
            $errorMessage = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">  <!-- Link to your CSS file -->
</head>
<body>
    <div class="form-container">
        <h2>Create an Account</h2>

        <?php if (isset($errorMessage)): ?>
            <p class="error"><?= $errorMessage; ?></p>
        <?php endif; ?>

        <form action="registration.php" method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" name="register">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
