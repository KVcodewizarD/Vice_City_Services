<?php

require_once __DIR__ . '/../includes/common.php';

requireRole('agency');

$agency_id = getCurrentUserId();
$stmt = $conn->prepare("
    SELECT 
        cars.id AS car_id,
        cars.vehicle_model,
        cars.vehicle_number,
        cars.rent_per_day,
        users.name AS customer_name,
        users.email AS customer_email,
        bookings.id AS booking_id,
        bookings.number_of_days,
        bookings.start_date,
        bookings.created_at AS booking_date
    FROM cars
    JOIN bookings ON cars.id = bookings.car_id
    JOIN users ON bookings.customer_id = users.id
    WHERE cars.agency_id = ?
    ORDER BY bookings.created_at DESC
");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$bookings_result = $stmt->get_result();

$total_bookings = $bookings_result->num_rows;

$total_revenue = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - Vice City Services</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <h1>Bookings Overview</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> | 
        <a href="list_cars.php">My Cars</a> | 
        <a href="../index.php">Home</a> | 
        <a href="../auth/logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <p>Agency: <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></p>
    
    <div style="background: #f0f0f0; padding: 15px; margin: 20px 0; border-radius: 5px;">
        <h3>Booking Statistics</h3>
        <p>📊 Total Bookings: <strong><?php echo $total_bookings; ?></strong></p>
    </div>
    
    <h2>All Bookings</h2>
    
    <?php if ($bookings_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Vehicle Model</th>
                    <th>Vehicle Number</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Number of Days</th>
                    <th>Rent/Day</th>
                    <th>Total Amount</th>
                    <th>Start Date</th>
                    <th>Booked On</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $bookings_result->data_seek(0);
                while ($booking = $bookings_result->fetch_assoc()): 
                    $total_amount = $booking['number_of_days'] * $booking['rent_per_day'];
                    $total_revenue += $total_amount;
                ?>
                    <tr>
                        <td>#<?php echo $booking['booking_id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($booking['vehicle_model']); ?></strong></td>
                        <td><?php echo htmlspecialchars($booking['vehicle_number']); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_email']); ?></td>
                        <td><?php echo $booking['number_of_days']; ?> days</td>
                        <td>₹<?php echo number_format($booking['rent_per_day'], 2); ?></td>
                        <td><strong>₹<?php echo number_format($total_amount, 2); ?></strong></td>
                        <td><?php echo date('d M Y', strtotime($booking['start_date'])); ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($booking['booking_date'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr style="background: #e8f5e9; font-weight: bold;">
                    <td colspan="7" style="text-align: right;">Total Revenue:</td>
                    <td colspan="3">₹<?php echo number_format($total_revenue, 2); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <br>
        <div style="background: #e8f5e9; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <h3>💰 Revenue Summary</h3>
            <p>Total Bookings: <strong><?php echo $total_bookings; ?></strong></p>
            <p>Total Revenue: <strong>₹<?php echo number_format($total_revenue, 2); ?></strong></p>
            <p>Average Booking Value: <strong>₹<?php echo number_format($total_revenue / $total_bookings, 2); ?></strong></p>
        </div>
        
    <?php else: ?>
        <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #666;">📋 No bookings yet.</h3>
            <p>No customers have booked your cars yet. Keep your fleet updated and competitive!</p>
            <br>
            <a href="add_car.php"><button>➕ Add More Cars</button></a>
            <a href="list_cars.php"><button>📋 View My Cars</button></a>
        </div>
    <?php endif; ?>
    
    <br>
    <p><a href="dashboard.php">← Back to Dashboard</a></p>
</body>
</html>
<?php
$stmt->close();
?>
