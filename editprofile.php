<?php

include 'Bdatabase.php';


session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


$user_id = $_SESSION['user_id'];

// Fetch current user details
$userQuery = "SELECT full_name, email, phone_number FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProfile'])) {
    $newName = $_POST['full_name'];
    $newEmail = $_POST['email'];
    $newPhone = $_POST['phone_number'];

    
    $updateQuery = "UPDATE Users SET full_name = ?, email = ?, phone_number = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('sssi', $newName, $newEmail, $newPhone, $user_id);
    $stmt->execute();

    // Redirect to the dashboard after updating the profile
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/edit.css">
</head>
<body>
    <header>
        <h1>Edit Your Profile</h1>
    </header>
    <main class="edit-profile-container">
        <section class="edit-profile">
            <form action="edit_profile.php" method="POST">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" value="<?= $user['full_name']; ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= $user['email']; ?>" required>

                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" value="<?= $user['phone_number']; ?>" required>

                <button type="submit" name="updateProfile">Update Profile</button>
            </form>
        </section>
    </main>
</body>
</html>
