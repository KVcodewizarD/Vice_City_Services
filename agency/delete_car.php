<?php
require_once __DIR__ . '/../includes/common.php';

requireRole('agency');

$agency_id = getCurrentUserId();

$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($car_id <= 0) {
    redirect('dashboard.php');
}

$verify_stmt = $conn->prepare("SELECT id, vehicle_model, vehicle_number FROM cars WHERE id = ? AND agency_id = ?");
$verify_stmt->bind_param("ii", $car_id, $agency_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows === 0) {
    redirect('dashboard.php');
}

$car = $verify_result->fetch_assoc();
$verify_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        $delete_stmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND agency_id = ?");
        $delete_stmt->bind_param("ii", $car_id, $agency_id);
        
        if ($delete_stmt->execute()) {
            if ($delete_stmt->affected_rows > 0) {
                $_SESSION['success'] = "Car '{$car['vehicle_model']}' (#{$car_id}) deleted successfully!";
            }
        }
        
        $delete_stmt->close();
        redirect('dashboard.php');
    } else {
        redirect('dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Car - Vice City Services</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <h1>Delete Car</h1>
    
    <nav>
        <a href="dashboard.php">Back to Dashboard</a> | 
        <a href="list_cars.php">View All Cars</a> | 
        <a href="view_bookings.php">View Bookings</a> | 
        <a href="../index.php">Home</a> | 
        <a href="../auth/logout.php">Logout</a>
    </nav>
    
    <hr>
    
    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 5px;">
        <h2 style="color: #856404;">⚠️ Confirm Deletion</h2>
        
        <p>Are you sure you want to delete this car? This action cannot be undone.</p>
        
        <table style="margin: 20px 0;">
            <tr>
                <th>Car ID:</th>
                <td>#<?php echo $car['id']; ?></td>
            </tr>
            <tr>
                <th>Vehicle Model:</th>
                <td><?php echo htmlspecialchars($car['vehicle_model']); ?></td>
            </tr>
            <tr>
                <th>Vehicle Number:</th>
                <td><?php echo htmlspecialchars($car['vehicle_number']); ?></td>
            </tr>
        </table>
        
        <p style="color: #dc3545; font-weight: bold;">
            ⚠️ Warning: All bookings associated with this car will also be deleted due to foreign key constraints.
        </p>
        
        <form method="POST" action="" style="margin-top: 20px;">
            <input type="hidden" name="confirm_delete" value="yes">
            <button type="submit" style="background: #dc3545; padding: 10px 20px;">
                🗑️ Yes, Delete This Car
            </button>
            <a href="dashboard.php">
                <button type="button" style="background: #6c757d; padding: 10px 20px;">
                    ✖️ No, Cancel
                </button>
            </a>
        </form>
    </div>
</body>
</html>
