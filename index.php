<?php
require_once __DIR__ . '/includes/common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vice City Services - Premium Car Rental</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="public/modern-style.css">
</head>
<body>
    
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <nav class="navbar navbar-expand-lg modern-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-car-front-fill me-2"></i>Vice City Services
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="public/available_cars.php">
                            <i class="bi bi-car-front me-1"></i>Browse Cars
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (hasRole('customer')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="customer/dashboard.php">
                                    <i class="bi bi-speedometer2 me-1"></i>My Dashboard
                                </a>
                            </li>
                        <?php elseif (hasRole('agency')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="agency/dashboard.php">
                                    <i class="bi bi-building me-1"></i>Agency Dashboard
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-plus me-1"></i>Register
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="auth/register.php">As Customer</a></li>
                                <li><a class="dropdown-item" href="auth/register_agency.php">As Agency</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <section class="hero-section">
        <div class="container">
            <div class="hero-content reveal">
                <?php if (isLoggedIn()): ?>
                    <h1 class="hero-title">Welcome Back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>!</h1>
                    <p class="hero-subtitle">
                        You're logged in as <strong><?php echo htmlspecialchars(getCurrentUserRole()); ?></strong>
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                        <a href="public/available_cars.php" class="btn-modern">
                            <i class="bi bi-car-front me-2"></i>Browse Our Fleet
                        </a>
                        <?php if (hasRole('customer')): ?>
                            <a href="customer/dashboard.php" class="btn-modern btn-secondary">
                                <i class="bi bi-calendar-check me-2"></i>My Bookings
                            </a>
                        <?php elseif (hasRole('agency')): ?>
                            <a href="agency/dashboard.php" class="btn-modern btn-secondary">
                                <i class="bi bi-building me-2"></i>My Dashboard
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <h1 class="hero-title">Premium Car Rental Experience</h1>
                    <p class="hero-subtitle">
                        Discover luxury and comfort with our premium fleet.<br>
                        Your journey begins here.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                        <a href="public/available_cars.php" class="btn-modern">
                            <i class="bi bi-car-front me-2"></i>Browse Cars
                        </a>
                        <a href="auth/register.php" class="btn-modern btn-secondary">
                            <i class="bi bi-person-plus me-2"></i>Get Started
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <h2 class="section-title reveal">Why Choose Us</h2>
            <div class="row g-4">
                <div class="col-md-4 reveal">
                    <div class="glass-card text-center">
                        <div class="mb-3">
                            <i class="bi bi-shield-check" style="font-size: 3rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        </div>
                        <h3>Trusted Service</h3>
                        <p class="text-muted">Verified agencies and secure bookings for your peace of mind.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="glass-card text-center">
                        <div class="mb-3">
                            <i class="bi bi-wallet2" style="font-size: 3rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        </div>
                        <h3>Best Prices</h3>
                        <p class="text-muted">Competitive rates with no hidden fees. Transparent pricing.</p>
                    </div>
                </div>
                <div class="col-md-4 reveal">
                    <div class="glass-card text-center">
                        <div class="mb-3">
                            <i class="bi bi-headset" style="font-size: 3rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        </div>
                        <h3>24/7 Support</h3>
                        <p class="text-muted">Round-the-clock customer support for all your needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="modern-footer">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Vice City Services. All rights reserved.</p>
            <p class="mb-0"><small>Premium Car Rental Platform</small></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="public/animations.js"></script>
</body>
</html>
