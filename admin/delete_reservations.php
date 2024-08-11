<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';
require_once '../includes/security_headers.php';

// Verificar si el usuario es administrador
if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('login_view.php');
}

$conn = getDBConnection();

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Procesar la solicitud de eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    $sql = "DELETE FROM reservations WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $reservation_id);
        
        if ($stmt->execute()) {
            redirectTo('reservations_view_admin.php');
            exit();
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
    }
    $stmt->close();
}

$conn->close();
?>