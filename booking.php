<?php
// Include database connection
include 'Bdatabase.php'; 

// Fetch the service, therapist, and availability
$services = [];
$therapists = [];
$availabilityStatus = ''; // To store availability status message

// Fetch services (for the dropdown selection)
$serviceQuery = "SELECT service_id AS id, service_name AS name, price AS price FROM Services";
$serviceResult = $conn->query($serviceQuery);
if ($serviceResult && $serviceResult->num_rows > 0) {
    while ($row = $serviceResult->fetch_assoc()) {
        $services[] = $row;
    }
}

// Fetch therapists (for the dropdown selection)
$therapistQuery = "SELECT user_id AS id, full_name AS name FROM Users";
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
        $availabilityStatus = 'The therapist is available for this time slot.';
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
    <title>Check Availability</title>
    <link rel="stylesheet" href="css/booking.css">
</head>
<body>
    <header>
        <h1>Check Therapist Availability</h1>
    </header>
    <main class="booking-container">
        <?php if ($availabilityStatus): ?>
            <p class="availability-status"><?= $availabilityStatus; ?></p>
        <?php endif; ?>

        <form action="check_availability.php" method="POST" class="booking-form">
            <!-- Step 1: Select Service and Therapist -->
            <div class="form-step">
                <h2>Step 1: Select Service and Therapist</h2>
                <label for="service">Choose a Service:</label>
                <select name="service" id="service" required>
                    <option value="">-- Select a Service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= $service['name'] ?> - $<?= $service['price'] ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="therapist">Choose a Therapist:</label>
                <select name="therapist" id="therapist" required>
                    <option value="">-- Select a Therapist --</option>
                    <?php foreach ($therapists as $therapist): ?>
                        <option value="<?= $therapist['id'] ?>"><?= $therapist['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Step 2: Choose Date and Time -->
            <div class="form-step">
                <h2>Step 2: Choose Date and Time</h2>
                <label for="date">Choose a Date:</label>
                <input type="date" name="date" id="date" required>

                <label for="time">Choose a Time:</label>
                <select name="time" id="time" required>
                    <option value="">-- Select a Time Slot --</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="12:00:00">12:00 PM</option>
                    <option value="2:00:00">2:00 PM</option>
                    <option value="4:00:00">4:00 PM</option>
                </select>
            </div>

            <!-- Step 3: Confirmation -->
            <div class="form-step">
                <h2>Step 3: Confirmation</h2>
                <button type="submit" name="confirm" class="confirm-btn">Check Availability</button>
            </div>
        </form>
    </main>
</body>
</html>
