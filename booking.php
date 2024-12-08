<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'Bdatabase.php'; 

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$services = []; 
$therapists = [];
$availabilityStatus = ''; 

// Fetch services (for the dropdown selection)
$serviceQuery = "SELECT service_id AS id, service_name AS name, price AS price FROM Services";
$serviceResult = $conn->query($serviceQuery);
if ($serviceResult && $serviceResult->num_rows > 0) {
    while ($row = $serviceResult->fetch_assoc()) {
        $services[] = $row;
    }
}

// Fetch therapists (for the dropdown selection)
$therapistQuery = "SELECT user_id AS id, full_name AS name FROM Users WHERE role = 'therapist'";
$therapistResult = $conn->query($therapistQuery);
if ($therapistResult && $therapistResult->num_rows > 0) {
    while ($row = $therapistResult->fetch_assoc()) {
        $therapists[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $serviceId = $_POST['service'];
    $therapistId = $_POST['therapist'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Check therapist availability
    $availabilityQuery = "SELECT * FROM Availability 
                           WHERE therapist_id = ? 
                           AND date = ? 
                           AND start_time <= ? 
                           AND end_time >= ?";
    $stmt = $conn->prepare($availabilityQuery);
    $stmt->bind_param('isss', $therapistId, $date, $time, $time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Insert booking into the database
        $insertBookingQuery = "INSERT INTO Appointments (user_id, service_id, therapist_id, appointment_date, start_time) 
                               VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertBookingQuery);
        $insertStmt->bind_param('iiiss', $user_id, $serviceId, $therapistId, $date, $time);

        if ($insertStmt->execute()) {
            $availabilityStatus = 'Appointment successfully booked!';
        } else {
            $availabilityStatus = 'Error booking appointment. Please try again later.';
        }
    } else {
        $availabilityStatus = 'The therapist is not available for this time slot.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="css/booking.css">
</head>
<body>
    <header>
        <h1>Book an Appointment</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a> | 
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <form method="POST" action="booking.php">
            <label for="service">Select Service:</label>
            <select name="service" id="service" required>
                <?php foreach ($services as $service): ?>
                    <option value="<?= htmlspecialchars($service['id']); ?>">
                        <?= htmlspecialchars($service['name']) . " - $" . htmlspecialchars($service['price']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="therapist">Select Therapist:</label>
            <select name="therapist" id="therapist" required>
                <?php foreach ($therapists as $therapist): ?>
                    <option value="<?= htmlspecialchars($therapist['id']); ?>">
                        <?= htmlspecialchars($therapist['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="date">Select Date:</label>
            <input type="date" name="date" id="date" required>

            <label for="time">Select Time:</label>
            <input type="time" name="time" id="time" required>

            <button type="submit" name="confirm">Confirm Booking</button>
        </form>

        <?php if (!empty($availabilityStatus)): ?>
            <p><?= htmlspecialchars($availabilityStatus); ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
