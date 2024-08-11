<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('login_view.php');
}

$conn = getDBConnection();

// Procesar la solicitud de eliminación
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    validateCSRFToken($_POST['csrf_token']);

    $sql = "DELETE FROM restaurants WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = trim($_POST["id"]);
        
        if ($stmt->execute()) {
            redirectTo('restaurants_view_admin.php');
            exit();
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
    }
    $stmt->close();
} else {
    // Verificar si se proporcionó un ID válido
    if (empty(trim($_GET["id"]))) {
        redirectTo('restaurants_view_admin.php');
        exit();
    }
}

$conn->close();
?>

