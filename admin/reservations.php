<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('login_view.php');
}

$conn = getDBConnection();

// Obtener todas las reservas
$sql = "SELECT r.id, u.username, res.name as restaurant_name, r.reservation_date, r.reservation_time, r.party_size, r.status 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        JOIN restaurants res ON r.restaurant_id = res.id 
        ORDER BY r.reservation_date DESC, r.reservation_time DESC";
$result = $conn->query($sql);
$reservations = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>


