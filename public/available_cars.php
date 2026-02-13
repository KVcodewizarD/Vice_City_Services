<?php

require_once __DIR__ . '/../includes/common.php';

$stmt = $conn->prepare("
    SELECT c.*, u.name as agency_name 
    FROM cars c 
    JOIN users u ON c.agency_id = u.id 
    ORDER BY c.created_at DESC
");
$stmt->execute();
$cars_result = $stmt->get_result();
$is_logged_in = isLoggedIn();
$user_role = $is_logged_in ? getCurrentUserRole() : null;
$is_customer = $user_role === 'customer';
$is_agency = $user_role === 'agency';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Cars - Vice City Services</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="modern-style.css">
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
                        <a class="nav-link active" href="available_cars.php">
                            <i class="bi bi-car-front me-1"></i>Browse Cars
                        </a>
                    </li>
                    <?php if ($is_logged_in): ?>
                        <?php if ($is_customer): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../customer/dashboard.php">
                                    <i class="bi bi-speedometer2 me-1"></i>My Dashboard
                                </a>
                            </li>
                        <?php elseif ($is_agency): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../agency/dashboard.php">
                                    <i class="bi bi-building me-1"></i>Agency Dashboard
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/login.php">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-plus me-1"></i>Register
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="../auth/register.php">As Customer</a></li>
                                <li><a class="dropdown-item" href="../auth/register_agency.php">As Agency</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="text-center mb-5 reveal">
            <?php if ($is_logged_in): ?>
                <h1 class="hero-title" style="font-size: 2.5rem;">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
            <?php else: ?>
                <h1 class="hero-title" style="font-size: 2.5rem;">Available Cars for Rent</h1>
                <p class="hero-subtitle">Browse our available cars. <a href="../auth/login.php">Login</a> or <a href="../auth/register.php">Register</a> to book.</p>
            <?php endif; ?>
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

        <h2 class="section-title reveal">Our Premium Fleet</h2>
        
        <?php if ($cars_result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($car = $cars_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 reveal">
                        <div class="car-card">
                            <div class="car-card-header">
                                <h3><i class="bi bi-car-front me-2"></i><?php echo htmlspecialchars($car['vehicle_model']); ?></h3>
                            </div>
                            <div class="car-card-body">
                                <div class="car-info-row">
                                    <span class="car-info-label"><i class="bi bi-123 me-2"></i>Vehicle No:</span>
                                    <span class="car-info-value"><?php echo htmlspecialchars($car['vehicle_number']); ?></span>
                                </div>
                                <div class="car-info-row">
                                    <span class="car-info-label"><i class="bi bi-building me-2"></i>Agency:</span>
                                    <span class="car-info-value"><?php echo htmlspecialchars($car['agency_name']); ?></span>
                                </div>
                                <div class="car-info-row">
                                    <span class="car-info-label"><i class="bi bi-people me-2"></i>Seats:</span>
                                    <span class="car-info-value"><?php echo $car['seating_capacity']; ?> persons</span>
                                </div>
                            </div>
                            <div class="car-card-footer">
                                <div class="price-tag">₹<?php echo number_format($car['rent_per_day'], 2); ?><small style="font-size: 1rem;">/day</small></div>
                                
                                <?php if ($is_customer): ?>
                                    <form method="POST" action="../customer/rent_car.php">
                                        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                        
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small">Days:</label>
                                                <select name="number_of_days" class="form-select form-select-sm" required>
                                                    <?php for ($i = 1; $i <= 30; $i++): ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Start:</label>
                                                <input type="date" name="start_date" class="form-control form-control-sm" 
                                                       min="<?php echo date('Y-m-d'); ?>" 
                                                       value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn-modern btn-success w-100">
                                            <i class="bi bi-calendar-check me-2"></i>Rent Now
                                        </button>
                                    </form>
                                <?php elseif (!$is_logged_in): ?>
                                    <a href="../auth/login.php" class="btn-modern w-100">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Login to Rent
                                    </a>
                                <?php elseif ($is_agency): ?>
                                    <button class="btn-modern w-100" disabled style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="bi bi-slash-circle me-2"></i>Not Available for Agencies
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 reveal">
                <div class="glass-card d-inline-block px-5 py-4">
                    <i class="bi bi-car-front" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h3 class="mt-3">No cars available at the moment.</h3>
                    <p class="text-muted">Please check back later!</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($is_customer): ?>
            <hr class="my-5">
            <h2 class="section-title reveal">My Recent Bookings</h2>
            <?php
            $customer_id = getCurrentUserId();
            $bookings_stmt = $conn->prepare("
                SELECT b.*, c.vehicle_model, c.vehicle_number, c.rent_per_day, u.name as agency_name 
                FROM bookings b 
                JOIN cars c ON b.car_id = c.id 
                JOIN users u ON c.agency_id = u.id 
                WHERE b.customer_id = ? 
                ORDER BY b.created_at DESC
                LIMIT 5
            ");
            $bookings_stmt->bind_param("i", $customer_id);
            $bookings_stmt->execute();
            $bookings_result = $bookings_stmt->get_result();
            ?>
            
            <?php if ($bookings_result->num_rows > 0): ?>
                <div class="modern-table reveal">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Car</th>
                                <th>Agency</th>
                                <th>Days</th>
                                <th>Total Cost</th>
                                <th>Start Date</th>
                                <th>Booked On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                                <?php $total_cost = $booking['number_of_days'] * $booking['rent_per_day']; ?>
                                <tr>
                                    <td><strong>#<?php echo $booking['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($booking['vehicle_model']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['vehicle_number']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['agency_name']); ?></td>
                                    <td><?php echo $booking['number_of_days']; ?> days</td>
                                    <td><strong class="text-primary">₹<?php echo number_format($total_cost, 2); ?></strong></td>
                                    <td><?php echo date('d M Y', strtotime($booking['start_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($booking['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-4 reveal">
                    <a href="../customer/dashboard.php" class="btn-modern">
                        <i class="bi bi-list-ul me-2"></i>View All Bookings
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center py-4 reveal">
                    <p class="text-muted">No bookings yet. Rent your first car now!</p>
                </div>
            <?php endif; ?>
            
            <?php $bookings_stmt->close(); ?>
        <?php endif; ?>
    </div>

    <footer class="modern-footer">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Vice City Services. All rights reserved.</p>
            <p class="mb-0"><small>Premium Car Rental Platform</small></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="animations.js"></script>
</body>
</html>
<?php
$stmt->close();
?>
