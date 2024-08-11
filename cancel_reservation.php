<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';

// Verificar si el usuario ha iniciado sesión
if (!isLoggedIn()) {
    redirectTo('login_view.php');
}

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['id'];

// Verificar si la reserva existe y pertenece al usuario actual
$conn = getDBConnection();
$sql = "SELECT * FROM reservations WHERE id = ? AND user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $reservation_id, $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($reservation = $result->fetch_assoc()) {
            // La reserva existe y pertenece al usuario actual
            // Proceder con la cancelación
            $sql = "DELETE FROM reservations WHERE id = ?";
            if ($delete_stmt = $conn->prepare($sql)) {
                $delete_stmt->bind_param("i", $reservation_id);
                if ($delete_stmt->execute()) {
                    // Reserva cancelada exitosamente
                    $_SESSION['success_message'] = "La reserva ha sido cancelada exitosamente.";
                } else {
                    $_SESSION['error_message'] = "Ocurrió un error al cancelar la reserva. Por favor, inténtelo de nuevo.";
                }
                $delete_stmt->close();
            } else {
                $_SESSION['error_message'] = "Ocurrió un error al preparar la consulta de cancelación.";
            }
        } else {
            $_SESSION['error_message'] = "La reserva no existe o no pertenece al usuario actual.";
        }
    } else {
        $_SESSION['error_message'] = "Ocurrió un error al ejecutar la consulta.";
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Ocurrió un error al preparar la consulta.";
}

$conn->close();

// Redirigir al perfil del usuario
redirectTo('./views/profile_view.php');
