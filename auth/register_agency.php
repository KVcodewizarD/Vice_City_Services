<?php

require_once __DIR__ . '/../includes/common.php';

if (isLoggedIn()) {
    redirect('../index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'agency';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $success = 'Agency registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
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
    <title>Agency Registration - Vice City Services</title>
    
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
                            <i class="bi bi-building" style="font-size: 4rem; background: var(--success-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        </div>
                        <h1 class="hero-title" style="font-size: 2rem;">Grow Your Business</h1>
                        <p class="hero-subtitle">Agency Registration</p>
                    </div>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-modern" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-modern" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        </div>
                        <div class="text-center mb-4">
                            <a href="login.php" class="btn-modern">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login Now
                            </a>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="name" class="form-label">
                                <i class="bi bi-building me-1"></i>Agency Name
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="name" 
                                   name="name" 
                                   placeholder="Enter your agency name"
                                   required>
                            <div class="invalid-feedback">
                                Please enter your agency name.
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter your agency email"
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
                                   placeholder="Create a password (min. 6 characters)"
                                   minlength="6"
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Minimum 6 characters required
                            </div>
                            <div class="invalid-feedback">
                                Password must be at least 6 characters.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="bi bi-lock-fill me-1"></i>Confirm Password
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Confirm your password"
                                   minlength="6"
                                   required>
                            <div class="invalid-feedback">
                                Please confirm your password.
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn-modern btn-lg" style="background: var(--success-gradient);">
                                <i class="bi bi-building me-2"></i>Register as Agency
                            </button>
                        </div>

                        <div class="text-center mb-4">
                            <hr style="opacity: 0.2;">
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-2">
                                Already have an account? 
                                <a href="login.php" class="text-decoration-none fw-bold">Login here</a>
                            </p>
                            <p class="text-muted mb-0">
                                Are you a customer? 
                                <a href="register.php" class="text-decoration-none fw-bold">Register as Customer</a>
                            </p>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4 reveal">
                    <p class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Join our network of premium car rental agencies
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
                    var password = document.getElementById('password').value;
                    var confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (password !== confirmPassword) {
                        event.preventDefault();
                        event.stopPropagation();
                        document.getElementById('confirm_password').setCustomValidity('Passwords do not match');
                    } else {
                        document.getElementById('confirm_password').setCustomValidity('');
                    }
                    
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
        
        document.getElementById('confirm_password').addEventListener('input', function() {
            this.setCustomValidity('');
        });
    </script>
</body>
</html>
