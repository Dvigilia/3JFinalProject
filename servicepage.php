<?php
// Include database connection
include 'Bdatabase.php';

// Initialize variables for filters and sorting
$filterType = $_GET['type'] ?? null;
$priceMin = $_GET['price_min'] ?? null;
$priceMax = $_GET['price_max'] ?? null;
$duration = $_GET['duration'] ?? null;
$sortBy = $_GET['sort'] ?? 'service_name';

// Build query dynamically based on filters
$query = "SELECT service_id, service_name, description, price, duration, image_path FROM Services WHERE 1=1";
if ($filterType) $query .= " AND service_name LIKE '%$filterType%'";
if ($priceMin) $query .= " AND price >= $priceMin";
if ($priceMax) $query .= " AND price <= $priceMax";
if ($duration) $query .= " AND duration = $duration";
$query .= " ORDER BY " . ($sortBy === 'price' ? 'price' : ($sortBy === 'duration' ? 'duration' : 'service_name'));

// Fetch services
$result = $conn->query($query);
$Services = $result && $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="css/servicepage.css">
</head>
<body>
<header>
    <h1>Our Services</h1>
</header>
<main class="services-container">
    <!-- Filter Section -->
    <aside class="filters">
        <form method="GET" action="">
            <h2>Filter Services</h2>
            <label for="price_min">Price Range:</label>
            <input type="number" name="price_min" id="price_min" placeholder="Min" value="<?= htmlspecialchars($priceMin) ?>">
            <input type="number" name="price_max" id="price_max" placeholder="Max" value="<?= htmlspecialchars($priceMax) ?>">

            <label for="duration">Duration (mins):</label>
            <input type="number" name="duration" id="duration" placeholder="e.g., 60" value="<?= htmlspecialchars($duration) ?>">

            <label for="sort">Sort By:</label>
            <select name="sort" id="sort">
                <option value="service_name" <?= $sortBy === 'service_name' ? 'selected' : '' ?>>Name</option>
                <option value="price" <?= $sortBy === 'price' ? 'selected' : '' ?>>Price</option>
                <option value="duration" <?= $sortBy === 'duration' ? 'selected' : '' ?>>Duration</option>
            </select>

            <button type="submit">Apply Filters</button>
        </form>
    </aside>

    <!-- Services Section -->
    <section class="services">
        <?php if (!empty($Services)): ?>
            <?php foreach ($Services as $service): ?>
                <div class="service-card">
                    <img src="<?= htmlspecialchars($service['image_path']) ?>" alt="<?= htmlspecialchars($service['service_name']) ?>" class="service-image">
                    <h2 class="service-title"><?= htmlspecialchars($service['service_name']) ?></h2>
                    <p class="service-price">$<?= htmlspecialchars($service['price']) ?></p>
                    <p class="service-duration"><?= htmlspecialchars($service['duration']) ?> minutes</p>
                    <p class="service-description"><?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...</p>
                    <a href="booking.php?service_id=<?= $service['service_id'] ?>" class="book-now-button">Book Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No services available. Adjust filters to see results.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
