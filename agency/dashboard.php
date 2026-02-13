<?php

require_once __DIR__ . '/../includes/common.php';

requireRole('agency');

$agency_id = getCurrentUserId();

$stmt = $conn->prepare("SELECT * FROM cars WHERE agency_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$cars_result = $stmt->get_result();

$total_cars = $cars_result->num_rows;

$bookings_stmt = $conn->prepare("
    SELECT COUNT(*) as total_bookings, SUM(b.number_of_days * c.rent_per_day) as total_revenue
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    WHERE c.agency_id = ?
");
$bookings_stmt->bind_param("i", $agency_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();
$stats = $bookings_result->fetch_assoc();
$total_bookings = $stats['total_bookings'] ?? 0;
$total_revenue = $stats['total_revenue'] ?? 0;
$bookings_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Dashboard - Vice City Services</title>
    
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
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-building me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="list_cars.php">
                            <i class="bi bi-car-front me-1"></i>My Cars
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_bookings.php">
                            <i class="bi bi-calendar-check me-1"></i>Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_car.php">
                            <i class="bi bi-plus-circle me-1"></i>Add Car
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
            <h1 class="hero-title" style="font-size: 2.5rem;">Agency Dashboard</h1>
            <p class="hero-subtitle">Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>!</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-modern reveal" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-modern reveal" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4 reveal">
                <div class="stats-card">
                    <i class="bi bi-car-front-fill" style="font-size: 3rem; opacity: 0.8;"></i>
                    <div class="stats-number" data-target="<?php echo $total_cars; ?>"><?php echo $total_cars; ?></div>
                    <div class="stats-label">Total Cars</div>
                </div>
            </div>
            <div class="col-md-4 reveal">
                <div class="stats-card" style="background: var(--secondary-gradient);">
                    <i class="bi bi-calendar-check" style="font-size: 3rem; opacity: 0.8;"></i>
                    <div class="stats-number" data-target="<?php echo $total_bookings; ?>"><?php echo $total_bookings; ?></div>
                    <div class="stats-label">Total Bookings</div>
                </div>
            </div>
            <div class="col-md-4 reveal">
                <div class="stats-card" style="background: var(--success-gradient);">
                    <i class="bi bi-currency-rupee" style="font-size: 3rem; opacity: 0.8;"></i>
                    <div class="stats-number">₹<?php echo number_format($total_revenue, 0); ?></div>
                    <div class="stats-label">Total Revenue</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6 reveal">
                <div class="glass-card text-center">
                    <i class="bi bi-plus-circle" style="font-size: 3rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                    <h3 class="mt-3">Add New Car</h3>
                    <p class="text-muted">Expand your fleet and increase bookings</p>
                    <a href="add_car.php" class="btn-modern">
                        <i class="bi bi-plus-lg me-2"></i>Add Car
                    </a>
                </div>
            </div>
            <div class="col-md-6 reveal">
                <div class="glass-card text-center">
                    <i class="bi bi-calendar-check" style="font-size: 3rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                    <h3 class="mt-3">View Bookings</h3>
                    <p class="text-muted">Manage and track all your bookings</p>
                    <a href="view_bookings.php" class="btn-modern btn-secondary">
                        <i class="bi bi-list-check me-2"></i>View Bookings
                    </a>
                </div>
            </div>
        </div>

        <h2 class="section-title reveal">Your Fleet</h2>
        
        <?php if ($cars_result->num_rows > 0): ?>
            <div class="modern-table reveal">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash me-1"></i>ID</th>
                            <th><i class="bi bi-car-front me-1"></i>Vehicle Model</th>
                            <th><i class="bi bi-123 me-1"></i>Vehicle Number</th>
                            <th><i class="bi bi-people me-1"></i>Seats</th>
                            <th><i class="bi bi-currency-rupee me-1"></i>Rent/Day</th>
                            <th><i class="bi bi-calendar me-1"></i>Added On</th>
                            <th><i class="bi bi-gear me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($car = $cars_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong class="text-primary">#<?php echo $car['id']; ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($car['vehicle_model']); ?></strong></td>
                                <td><?php echo htmlspecialchars($car['vehicle_number']); ?></td>
                                <td><span class="badge-modern badge bg-info"><?php echo $car['seating_capacity']; ?> seats</span></td>
                                <td><strong class="text-success">₹<?php echo number_format($car['rent_per_day'], 2); ?></strong></td>
                                <td><?php echo date('d M Y', strtotime($car['created_at'])); ?></td>
                                <td>
                                    <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="delete_car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this car?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 reveal">
                <div class="glass-card d-inline-block px-5 py-4">
                    <i class="bi bi-car-front" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h3 class="mt-3">No Cars Added Yet</h3>
                    <p class="text-muted">Start by adding your first car to the fleet</p>
                    <a href="add_car.php" class="btn-modern mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Add Your First Car
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
$stmt->close();
?>
