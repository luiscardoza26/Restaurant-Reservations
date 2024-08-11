<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirectTo('login_view.php');
}

$user_id = $_SESSION['id'];

$conn = getDBConnection();

// Eliminar las reservas del usuario
$sql = "DELETE FROM reservations WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Eliminar las reseñas del usuario
$sql = "DELETE FROM reviews WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Eliminar la cuenta del usuario
$sql = "DELETE FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Cerrar la sesión y redirigir al usuario
session_destroy();
redirectTo('index_view.php?message=account_deleted');
?>
