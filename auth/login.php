<?php

require_once __DIR__ . '/../includes/common.php';

if (isLoggedIn()) {
    redirect('../public/available_cars.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                
                redirect('../public/available_cars.php');
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vice City Services</title>
    
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

    <div class="position-absolute top-0 start-0 m-4">
        <a href="../index.php" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left me-1"></i>Back to Home
        </a>
    </div>

    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="glass-card reveal">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="bi bi-car-front-fill" style="font-size: 4rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        </div>
                        <h1 class="hero-title" style="font-size: 2rem;">Welcome Back</h1>
                        <p class="hero-subtitle">Login to Vice City Services</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-modern" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter your email"
                                   required>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock me-1"></i>Password
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required>
                            <div class="invalid-feedback">
                                Please enter your password.
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn-modern btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </div>

                        <div class="text-center mb-4">
                            <hr style="opacity: 0.2;">
                            <p class="text-muted mb-0">Don't have an account?</p>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <a href="register.php" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-person-plus me-1"></i>Customer
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="register_agency.php" class="btn btn-outline-success w-100">
                                    <i class="bi bi-building me-1"></i>Agency
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4 reveal">
                    <p class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Your data is secure with us
                    </p>
                </div>
            </div>
        </div>
    </div>

    <footer class="modern-footer" style="position: relative; margin-top: -80px;">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Vice City Services. All rights reserved.</p>
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
