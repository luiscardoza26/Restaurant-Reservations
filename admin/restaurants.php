<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('login_view.php');
}

$conn = getDBConnection();

// Obtener todos los restaurantes
$sql = "SELECT * FROM restaurants ORDER BY name ASC";
$result = $conn->query($sql);
$restaurants = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

