<?php
require_once __DIR__ . '/../includes/common.php';

requireRole('agency');

$agency_id = getCurrentUserId();
$error = '';
$success = '';

$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($car_id <= 0) {
    redirect('dashboard.php');
}
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND agency_id = ?");
$stmt->bind_param("ii", $car_id, $agency_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Car not found or you do not have permission to edit this car.';
    redirect('dashboard.php');
}

$car = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_model = sanitizeInput($_POST['vehicle_model'] ?? '');
    $vehicle_number = sanitizeInput($_POST['vehicle_number'] ?? '');
    $seating_capacity = isset($_POST['seating_capacity']) ? intval($_POST['seating_capacity']) : 0;
    $rent_per_day = isset($_POST['rent_per_day']) ? floatval($_POST['rent_per_day']) : 0;
    
    if (empty($vehicle_model)) {
        $error = 'Vehicle model is required';
    } elseif (empty($vehicle_number)) {
        $error = 'Vehicle number is required';
    } elseif ($seating_capacity <= 0 || $seating_capacity > 50) {
        $error = 'Seating capacity must be between 1 and 50';
    } elseif ($rent_per_day <= 0) {
        $error = 'Rent per day must be greater than 0';
    } else {
        $check_stmt = $conn->prepare("SELECT id FROM cars WHERE vehicle_number = ? AND id != ?");
        $check_stmt->bind_param("si", $vehicle_number, $car_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = 'Vehicle number already exists. Please use a unique vehicle number.';
            $check_stmt->close();
        } else {
            $check_stmt->close();
            
            $update_stmt = $conn->prepare("
                UPDATE cars 
                SET vehicle_model = ?, vehicle_number = ?, seating_capacity = ?, rent_per_day = ?
                WHERE id = ? AND agency_id = ?
            ");
            $update_stmt->bind_param("ssidii", $vehicle_model, $vehicle_number, $seating_capacity, $rent_per_day, $car_id, $agency_id);
            
            if ($update_stmt->execute()) {
                if ($update_stmt->affected_rows > 0) {
                    $car['vehicle_model'] = $vehicle_model;
                    $car['vehicle_number'] = $vehicle_number;
                    $car['seating_capacity'] = $seating_capacity;
                    $car['rent_per_day'] = $rent_per_day;
                } else {
                    $error = 'No changes were made or car not found.';
                }
            } else {
                $error = 'Failed to update car. Please try again.';
            }
            
            $update_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car - Vice City Services</title>
    
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
                        <a class="nav-link" href="dashboard.php">
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
            <h1 class="hero-title" style="font-size: 2.5rem;">
                <i class="bi bi-pencil-square me-2"></i>Edit Car
            </h1>
            <p class="hero-subtitle">Update vehicle details</p>
            <p class="text-muted">Agency: <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></p>
            <p class="text-muted">Car ID: <span class="badge bg-primary">#<?php echo $car_id; ?></span></p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-modern reveal" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-modern reveal" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="glass-card reveal">
                    <h2 class="section-title mb-4">
                        <i class="bi bi-car-front me-2"></i>Car Details
                    </h2>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="vehicle_model" class="form-label">
                                <i class="bi bi-car-front me-1"></i>Vehicle Model <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="vehicle_model" 
                                   name="vehicle_model" 
                                   value="<?php echo htmlspecialchars($car['vehicle_model']); ?>" 
                                   placeholder="e.g., Toyota Camry, Honda City, Hyundai Creta" 
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Enter the complete model name
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="vehicle_number" class="form-label">
                                <i class="bi bi-123 me-1"></i>Vehicle Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="vehicle_number" 
                                   name="vehicle_number" 
                                   value="<?php echo htmlspecialchars($car['vehicle_number']); ?>" 
                                   placeholder="e.g., MH01AB1234, DL01CD5678" 
                                   required>
                            <div class="form-text">
                                <i class="bi bi-exclamation-circle me-1"></i>Must be unique across all vehicles
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="seating_capacity" class="form-label">
                                    <i class="bi bi-people me-1"></i>Seating Capacity <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg" 
                                       id="seating_capacity" 
                                       name="seating_capacity" 
                                       value="<?php echo htmlspecialchars($car['seating_capacity']); ?>" 
                                       min="1" 
                                       max="50" 
                                       placeholder="e.g., 5, 7" 
                                       required>
                                <div class="form-text">
                                    <i class="bi bi-arrow-left-right me-1"></i>Between 1 and 50
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="rent_per_day" class="form-label">
                                    <i class="bi bi-currency-rupee me-1"></i>Rent per Day <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg" 
                                       id="rent_per_day" 
                                       name="rent_per_day" 
                                       value="<?php echo htmlspecialchars($car['rent_per_day']); ?>" 
                                       min="0.01" 
                                       step="0.01" 
                                       placeholder="e.g., 2500.00" 
                                       required>
                                <div class="form-text">
                                    <i class="bi bi-cash me-1"></i>Must be greater than 0
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3 justify-content-center mt-4">
                            <button type="submit" class="btn-modern">
                                <i class="bi bi-check-circle me-2"></i>Update Car
                            </button>
                            <a href="dashboard.php" class="btn-modern btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <span class="text-danger">*</span> Required fields
                            </small>
                        </div>
                    </form>
                    
                    <div class="mt-4 pt-4 border-top">
                        <div class="text-center text-muted">
                            <small>
                                <i class="bi bi-clock me-1"></i>Created on: 
                                <strong><?php echo date('d M Y, h:i A', strtotime($car['created_at'])); ?></strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-md-6 reveal">
                <div class="glass-card text-center">
                    <i class="bi bi-trash" style="font-size: 2.5rem; color: #dc3545; opacity: 0.8;"></i>
                    <h5 class="mt-3">Delete This Car</h5>
                    <p class="text-muted small mb-3">Permanently remove this vehicle from your fleet</p>
                    <a href="delete_car.php?id=<?php echo $car_id; ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this car? This action cannot be undone.');">
                        <i class="bi bi-trash me-2"></i>Delete Car
                    </a>
                </div>
            </div>
            <div class="col-md-6 reveal">
                <div class="glass-card text-center">
                    <i class="bi bi-calendar-check" style="font-size: 2.5rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                    <h5 class="mt-3">View Bookings</h5>
                    <p class="text-muted small mb-3">Check all bookings for your vehicles</p>
                    <a href="view_bookings.php" class="btn-modern btn-secondary">
                        <i class="bi bi-list-check me-2"></i>View Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="modern-footer">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Vice City Services. All rights reserved.</p>
            <p class="mb-0"><small>Premium Car Rental Platform</small></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../public/animations.js"></script>
    
    <script>
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>
