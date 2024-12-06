<?php
include 'Bdatabase.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    $service = $_POST['service'];
    $therapist = $_POST['therapist'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $promo = $_POST['promo'];
    $paymentMethod = $_POST['payment_method'];

    // Validate input fields
    if (empty($service) || empty($therapist) || empty($date) || empty($time) || empty($paymentMethod)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        // Insert appointment details
        $query = "INSERT INTO Appointments (user_id, therapist_id, service_id, appointment_date, start_time, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $userId = 1; // Replace with logged-in user ID
        $stmt->bind_param("iiiss", $userId, $therapist, $service, $date, $time);
        if ($stmt->execute()) {
            $appointmentId = $stmt->insert_id;

            // Fetch service price
            $priceQuery = "SELECT price FROM Services WHERE service_id = ?";
            $priceStmt = $conn->prepare($priceQuery);
            $priceStmt->bind_param("i", $service);
            $priceStmt->execute();
            $priceResult = $priceStmt->get_result();
            $priceRow = $priceResult->fetch_assoc();
            $amount = $priceRow['price'];

            // Insert payment details
            $paymentQuery = "INSERT INTO Payments (appointment_id, amount, payment_method, payment_status, payment_date) VALUES (?, ?, ?, 'paid', NOW())";
            $paymentStmt = $conn->prepare($paymentQuery);
            $paymentStmt->bind_param("ids", $appointmentId, $amount, $paymentMethod);
            if ($paymentStmt->execute()) {
                $successMessage = "Appointment and payment successfully processed!";
            } else {
                $errorMessage = "Failed to process payment.";
            }
        } else {
            $errorMessage = "Failed to book the appointment.";
        }
    }
}
?>
