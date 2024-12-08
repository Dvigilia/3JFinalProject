<?php
include 'Bdatabase.php';


session_start();


if (!isset($_SESSION['User_id'])) {
    header('Location: login.php');
    exit();
}

$bookingQuery = "SELECT * FROM Bookings";
$bookingResult = $conn->query($bookingQuery);

// Fetch all services
$serviceQuery = "SELECT * FROM Services";
$serviceResult = $conn->query($serviceQuery);

// Fetch all therapists' schedules
$scheduleQuery = "SELECT * FROM Therapist_Schedules";
$scheduleResult = $conn->query($scheduleQuery);

// Fetch payments
$paymentQuery = "SELECT * FROM Payments";
$paymentResult = $conn->query($paymentQuery);

// Handle booking actions (approve, cancel, reschedule)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approveBooking'])) {
        $bookingId = $_POST['bookingId'];
        $updateQuery = "UPDATE Bookings SET status = 'approved' WHERE booking_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        header('Location: admin_dashboard.php');
        exit();
    } elseif (isset($_POST['cancelBooking'])) {
        $bookingId = $_POST['bookingId'];
        $updateQuery = "UPDATE Bookings SET status = 'cancelled' WHERE booking_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        header('Location: admin_dashboard.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admindashboard.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#manageBookings">Manage Bookings</a></li>
                <li><a href="#manageServices">Manage Services</a></li>
                <li><a href="#therapistSchedule">Therapist Schedule</a></li>
                <li><a href="#payments">Payments</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Manage Bookings Section -->
        <section id="manageBookings">
            <h2>Manage Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $bookingResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['booking_id'] ?></td>
                            <td><?= $row['service_name'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                                <form action="admin_dashboard.php" method="POST" style="display:inline;">
                                    <button type="submit" name="approveBooking" value="Approve">Approve</button>
                                    <input type="hidden" name="bookingId" value="<?= $row['booking_id'] ?>">
                                </form>
                                <form action="admin_dashboard.php" method="POST" style="display:inline;">
                                    <button type="submit" name="cancelBooking" value="Cancel">Cancel</button>
                                    <input type="hidden" name="bookingId" value="<?= $row['booking_id'] ?>">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Manage Services Section -->
        <section id="manageServices">
            <h2>Manage Services</h2>
            <table>
                <thead>
                    <tr>
                        <th>Service ID</th>
                        <th>Service Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $serviceResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['service_id'] ?></td>
                            <td><?= $row['service_name'] ?></td>
                            <td><?= $row['price'] ?></td>
                            <td><?= $row['duration'] ?> mins</td>
                            <td>
                                <a href="edit_service.php?id=<?= $row['service_id'] ?>">Edit</a> | 
                                <a href="delete_service.php?id=<?= $row['service_id'] ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h3>Add New Service</h3>
            <form action="add_service.php" method="POST">
                <label for="service_name">Service Name:</label>
                <input type="text" name="service_name" required>
                <label for="price">Price:</label>
                <input type="number" name="price" required>
                <label for="duration">Duration (in mins):</label>
                <input type="number" name="duration" required>
                <button type="submit">Add Service</button>
            </form>
        </section>

        <!-- Therapist Schedule Section -->
        <section id="therapistSchedule">
            <h2>Therapist Schedule</h2>
            <table>
                <thead>
                    <tr>
                        <th>Therapist</th>
                        <th>Available Date</th>
                        <th>Available Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $scheduleResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['therapist_name'] ?></td>
                            <td><?= $row['available_date'] ?></td>
                            <td><?= $row['available_time'] ?></td>
                            <td>
                                <a href="edit_schedule.php?id=<?= $row['schedule_id'] ?>">Edit</a> |
                                <a href="delete_schedule.php?id=<?= $row['schedule_id'] ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h3>Add Therapist Availability</h3>
            <form action="add_schedule.php" method="POST">
                <label for="therapist_name">Therapist Name:</label>
                <input type="text" name="therapist_name" required>
                <label for="available_date">Available Date:</label>
                <input type="date" name="available_date" required>
                <label for="available_time">Available Time:</label>
                <input type="time" name="available_time" required>
                <button type="submit">Add Availability</button>
            </form>
        </section>

        <!-- Payments Section -->
        <section id="payments">
            <h2>Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $paymentResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['transaction_id'] ?></td>
                            <td><?= $row['user_name'] ?></td>
                            <td><?= $row['amount'] ?></td>
                            <td><?= $row['status'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
