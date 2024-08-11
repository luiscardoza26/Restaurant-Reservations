<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Verificar si el usuario ha iniciado sesión
if (!isLoggedIn()) {
    redirectTo('login_view.php');
}

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$email = '';
$nit = '';
$reservations = [];
$reviews = [];

$conn = getDBConnection();

// Obtener información del usuario
$sql = "SELECT email, nit FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $email = $row['email'];
            $nit = $row['nit'];
        }
    }
    $stmt->close();
}

// Obtener reservas del usuario
$sql = "SELECT r.*, res.name as restaurant_name 
        FROM reservations r 
        JOIN restaurants res ON r.restaurant_id = res.id 
        WHERE r.user_id = ? 
        ORDER BY r.reservation_date DESC, r.reservation_time DESC";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reservations[] = $row;
        }
    }
    $stmt->close();
}

// Obtener reseñas del usuario
$sql = "SELECT rev.*, res.name as restaurant_name 
        FROM reviews rev 
        JOIN restaurants res ON rev.restaurant_id = res.id 
        WHERE rev.user_id = ? 
        ORDER BY rev.created_at DESC";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
    }
    $stmt->close();
}

$conn->close();
?>

