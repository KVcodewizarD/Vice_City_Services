<?php

require_once __DIR__ . '/../includes/common.php';

requireRole('customer');

$customer_id = getCurrentUserId();
$error = '';
$success = '';
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($car_id <= 0) {
    redirect('../public/available_cars.php');
}

$stmt = $conn->prepare("
    SELECT c.*, u.name as agency_name 
    FROM cars c 
    JOIN users u ON c.agency_id = u.id 
    WHERE c.id = ?
");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('../public/available_cars.php');
}

$car = $result->fetch_assoc();
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number_of_days = isset($_POST['number_of_days']) ? intval($_POST['number_of_days']) : 0;
    $start_date = sanitizeInput($_POST['start_date'] ?? '');
    
    if ($number_of_days <= 0 || $number_of_days > 365) {
        $error = 'Number of days must be between 1 and 365';
    } elseif (empty($start_date)) {
        $error = 'Please select a start date';
    } elseif (strtotime($start_date) < strtotime(date('Y-m-d'))) {
        $error = 'Start date cannot be in the past';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO bookings (customer_id, car_id, number_of_days, start_date) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiis", $customer_id, $car_id, $number_of_days, $start_date);
        
        if ($stmt->execute()) {
            $success = 'Booking successful! Booking ID: #' . $stmt->insert_id;
        } else {
            $error = 'Booking failed. Please try again.';
        }
        
        $stmt->close();
    }
}

$total_cost = 0;
if (isset($_POST['number_of_days'])) {
    $total_cost = intval($_POST['number_of_days']) * $car['rent_per_day'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Car - Vice City Services</title>
    <link rel="stylesheet" href="../public/style.css">
    <script>
        function calculateTotal() {
            const days = document.getElementById('number_of_days').value;
            const rentPerDay = <?php echo $car['rent_per_day']; ?>;
            const total = days * rentPerDay;
            document.getElementById('total_cost').textContent = '₹' + total.toFixed(2);
        }
    </script>
</head>
<body>
    <h1>Book a Car</h1>
    
    <nav>
        <a href="../public/available_cars.php">Back to Available Cars</a> | 
        <a href="dashboard.php">My Dashboard</a> | 
        <a href="../auth/logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <p><a href="../public/available_cars.php">View My Bookings</a></p>
    <?php else: ?>
    
    <h2>Car Details</h2>
    <table>
        <tr>
            <th>Vehicle Model:</th>
            <td><?php echo htmlspecialchars($car['vehicle_model']); ?></td>
        </tr>
        <tr>
            <th>Vehicle Number:</th>
            <td><?php echo htmlspecialchars($car['vehicle_number']); ?></td>
        </tr>
        <tr>
            <th>Agency:</th>
            <td><?php echo htmlspecialchars($car['agency_name']); ?></td>
        </tr>
        <tr>
            <th>Seating Capacity:</th>
            <td><?php echo $car['seating_capacity']; ?> seats</td>
        </tr>
        <tr>
            <th>Rent per Day:</th>
            <td>₹<?php echo number_format($car['rent_per_day'], 2); ?></td>
        </tr>
    </table>
    
    <h2>Booking Details</h2>
    <form method="POST" action="">
        <div>
            <label for="start_date">Start Date:</label><br>
            <input type="date" id="start_date" name="start_date" 
                   min="<?php echo date('Y-m-d'); ?>" 
                   value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <br>
        <div>
            <label for="number_of_days">Number of Days:</label><br>
            <input type="number" id="number_of_days" name="number_of_days" 
                   min="1" max="365" value="1" required 
                   onchange="calculateTotal()" onkeyup="calculateTotal()">
        </div>
        <br>
        <div>
            <strong>Total Cost: </strong>
            <span id="total_cost">₹<?php echo number_format($car['rent_per_day'], 2); ?></span>
        </div>
        <br>
        <button type="submit">Confirm Booking</button>
    </form>
    
    <?php endif; ?>
</body>
</html>
