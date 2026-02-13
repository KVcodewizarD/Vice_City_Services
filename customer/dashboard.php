<?php

require_once __DIR__ . '/../includes/common.php';

requireRole('customer');

$customer_id = getCurrentUserId();
$bookings_stmt = $conn->prepare("
    SELECT b.*, c.vehicle_model, c.vehicle_number, c.rent_per_day, u.name as agency_name 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    JOIN users u ON c.agency_id = u.id 
    WHERE b.customer_id = ? 
    ORDER BY b.created_at DESC
");
$bookings_stmt->bind_param("i", $customer_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();


$total_bookings = $bookings_result->num_rows;

$total_spent = 0;
$bookings_result->data_seek(0);
while ($booking = $bookings_result->fetch_assoc()) {
    $total_spent += $booking['number_of_days'] * $booking['rent_per_day'];
}
$bookings_result->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Vice City Services</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="../public/modern-style.css">
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <nav class="navbar navbar-expand-lg modern-navbar">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="bi bi-car-front-fill me-2"></i>Vice City Services
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../public/available_cars.php">
                            <i class="bi bi-car-front me-1"></i>Browse Cars
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i>My Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="text-center mb-5 reveal">
            <h1 class="hero-title" style="font-size: 2.5rem;">Welcome Back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
            <p class="hero-subtitle">Manage your bookings and discover new cars</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4 reveal">
                <div class="stats-card">
                    <i class="bi bi-calendar-check" style="font-size: 3rem; opacity: 0.8;"></i>
                    <div class="stats-number" data-target="<?php echo $total_bookings; ?>"><?php echo $total_bookings; ?></div>
                    <div class="stats-label">Total Bookings</div>
                </div>
            </div>
            <div class="col-md-4 reveal">
                <div class="stats-card" style="background: var(--secondary-gradient);">
                    <i class="bi bi-wallet2" style="font-size: 3rem; opacity: 0.8;"></i>
                    <div class="stats-number">₹<?php echo number_format($total_spent, 0); ?></div>
                    <div class="stats-label">Total Spent</div>
                </div>
            </div>
            <div class="col-md-4 reveal">
                <div class="stats-card" style="background: var(--success-gradient);">
                    <i class="bi bi-car-front" style="font-size: 3rem; opacity: 0.8;"></i>
                    <div class="stats-number">
                        <a href="../public/available_cars.php" class="btn-modern btn-sm" style="background: rgba(255,255,255,0.3); border: 2px solid white;">
                            <i class="bi bi-plus-lg me-2"></i>Book New Car
                        </a>
                    </div>
                    <div class="stats-label">Rent More Cars</div>
                </div>
            </div>
        </div>

        <h2 class="section-title reveal">My Bookings History</h2>
        
        <?php if ($bookings_result->num_rows > 0): ?>
            <div class="modern-table reveal">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash me-1"></i>ID</th>
                            <th><i class="bi bi-car-front me-1"></i>Car Details</th>
                            <th><i class="bi bi-building me-1"></i>Agency</th>
                            <th><i class="bi bi-calendar-range me-1"></i>Duration</th>
                            <th><i class="bi bi-currency-rupee me-1"></i>Cost</th>
                            <th><i class="bi bi-calendar-event me-1"></i>Start Date</th>
                            <th><i class="bi bi-clock-history me-1"></i>Booked On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                            <?php $total_cost = $booking['number_of_days'] * $booking['rent_per_day']; ?>
                            <tr>
                                <td><strong class="text-primary">#<?php echo $booking['id']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['vehicle_model']); ?></strong><br>
                                    <small class="text-muted"><i class="bi bi-123 me-1"></i><?php echo htmlspecialchars($booking['vehicle_number']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['agency_name']); ?></td>
                                <td>
                                    <span class="badge-modern badge bg-primary"><?php echo $booking['number_of_days']; ?> days</span><br>
                                    <small class="text-muted">₹<?php echo number_format($booking['rent_per_day'], 2); ?>/day</small>
                                </td>
                                <td>
                                    <strong class="text-success" style="font-size: 1.1rem;">₹<?php echo number_format($total_cost, 2); ?></strong>
                                </td>
                                <td><?php echo date('d M Y', strtotime($booking['start_date'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 reveal">
                <div class="glass-card d-inline-block px-5 py-4">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h3 class="mt-3">No Bookings Yet</h3>
                    <p class="text-muted">Start your journey by renting your first car!</p>
                    <a href="../public/available_cars.php" class="btn-modern mt-3">
                        <i class="bi bi-car-front me-2"></i>Browse Available Cars
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="modern-footer">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Vice City Services. All rights reserved.</p>
            <p class="mb-0"><small>Premium Car Rental Platform</small></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../public/animations.js"></script>
</body>
</html>
<?php
$bookings_stmt->close();
?>
