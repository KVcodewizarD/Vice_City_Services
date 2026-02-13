<?php

require_once __DIR__ . '/../includes/common.php';

requireRole('agency');

$agency_id = getCurrentUserId();
$stmt = $conn->prepare("SELECT * FROM cars WHERE agency_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$cars_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cars - Vice City Services</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <h1>My Cars</h1>
    
    <nav>
        <a href="dashboard.php">Dashboard</a> | 
        <a href="add_car.php">Add New Car</a> | 
        <a href="view_bookings.php">View Bookings</a> | 
        <a href="../index.php">Home</a> | 
        <a href="../auth/logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <p>Agency: <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></p>
    <p>Total Cars: <strong><?php echo $cars_result->num_rows; ?></strong></p>
    
    <?php
    if (isset($_SESSION['success'])) {
        echo '<p class="success">' . htmlspecialchars($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    ?>
    
    <h2>Car List</h2>
    
    <?php if ($cars_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle Model</th>
                    <th>Vehicle Number</th>
                    <th>Seating Capacity</th>
                    <th>Rent per Day</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($car = $cars_result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $car['id']; ?></td>
                        <td><?php echo htmlspecialchars($car['vehicle_model']); ?></td>
                        <td><strong><?php echo htmlspecialchars($car['vehicle_number']); ?></strong></td>
                        <td><?php echo $car['seating_capacity']; ?> seats</td>
                        <td>₹<?php echo number_format($car['rent_per_day'], 2); ?></td>
                        <td><?php echo date('d M Y', strtotime($car['created_at'])); ?></td>
                        <td>
                            <a href="edit_car.php?id=<?php echo $car['id']; ?>">
                                <button style="padding: 5px 10px; background: #007bff;">Edit</button>
                            </a>
                            <a href="delete_car.php?id=<?php echo $car['id']; ?>">
                                <button style="padding: 5px 10px; background: #dc3545;">Delete</button>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 5px;">
            <p style="font-size: 18px; color: #666;">No cars added yet.</p>
            <p><a href="add_car.php"><button>Add Your First Car</button></a></p>
        </div>
    <?php endif; ?>
    
    <br>
    <p><a href="add_car.php"><button>➕ Add New Car</button></a></p>
</body>
</html>
<?php
$stmt->close();
?>
