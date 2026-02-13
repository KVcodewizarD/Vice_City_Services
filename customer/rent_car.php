<?php

require_once __DIR__ . '/../includes/common.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to rent a car.';
    redirect('../auth/login.php');
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['error'] = 'Only customers can rent cars.';
    redirect('../public/available_cars.php');
}

$customer_id = $_SESSION['user_id'];
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../public/available_cars.php');
}

$car_id = isset($_POST['car_id']) ? intval($_POST['car_id']) : 0;
$number_of_days = isset($_POST['number_of_days']) ? intval($_POST['number_of_days']) : 0;
$start_date = sanitizeInput($_POST['start_date'] ?? '');
if ($car_id <= 0) {
    $_SESSION['error'] = 'Invalid car selected.';
    redirect('../public/available_cars.php');
}

if ($number_of_days <= 0) {
    $_SESSION['error'] = 'Number of days must be greater than 0.';
    redirect('../public/available_cars.php');
}

if ($number_of_days > 365) {
    $_SESSION['error'] = 'Number of days cannot exceed 365.';
    redirect('../public/available_cars.php');
}

if (empty($start_date)) {
    $_SESSION['error'] = 'Please select a start date.';
    redirect('../public/available_cars.php');
}

$today = date('Y-m-d');
if (strtotime($start_date) < strtotime($today)) {
    $_SESSION['error'] = 'Start date cannot be in the past. Please select today or a future date.';
    redirect('../public/available_cars.php');
}

$verify_stmt = $conn->prepare("SELECT id, vehicle_model, rent_per_day FROM cars WHERE id = ?");
$verify_stmt->bind_param("i", $car_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows === 0) {
    $verify_stmt->close();
    $_SESSION['error'] = 'Car not found. It may have been removed.';
    redirect('../public/available_cars.php');
}

$car = $verify_result->fetch_assoc();
$verify_stmt->close();
$total_cost = $number_of_days * $car['rent_per_day'];

$insert_stmt = $conn->prepare("
    INSERT INTO bookings (customer_id, car_id, number_of_days, start_date) 
    VALUES (?, ?, ?, ?)
");
$insert_stmt->bind_param("iiis", $customer_id, $car_id, $number_of_days, $start_date);

if ($insert_stmt->execute()) {
    $booking_id = $insert_stmt->insert_id;
    $_SESSION['success'] = "Booking successful! Booking ID: #$booking_id | Car: {$car['vehicle_model']} | Days: $number_of_days | Total: ₹" . number_format($total_cost, 2);
    $insert_stmt->close();
    redirect('../public/available_cars.php');
} else {
    $_SESSION['error'] = 'Booking failed. Please try again.';
    $insert_stmt->close();
    redirect('../public/available_cars.php');
}
?>
