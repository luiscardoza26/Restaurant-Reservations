<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';

// Verificar si el usuario ha iniciado sesión
if (!isLoggedIn()) {
    redirectTo('login_view.php');
}

$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['id'];

$conn = getDBConnection();

// Obtener la información de la reseña
$sql = "SELECT r.*, res.name as restaurant_name 
        FROM reviews r 
        JOIN restaurants res ON r.restaurant_id = res.id 
        WHERE r.id = ? AND r.user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $review_id, $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($review = $result->fetch_assoc()) {
            // La reseña existe y pertenece al usuario actual
        } else {
            // La reseña no existe o no pertenece al usuario actual
            $_SESSION['error_message'] = "No se encontró la reseña o no tienes permiso para editarla.";
            redirectTo('profile_view.php');
        }
    } else {
        echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
    }
    $stmt->close();
} else {
    echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = trim($_POST['comment']);

    // Validar los datos
    if ($rating < 1 || $rating > 5) {
        $review_error = "La calificación debe estar entre 1 y 5.";
    } elseif (empty($comment)) {
        $review_error = "El comentario no puede estar vacío.";
    } else {
        // Actualizar la reseña
        $sql = "UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isii", $rating, $comment, $review_id, $user_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "La reseña ha sido actualizada exitosamente.";
                redirectTo('profile_view.php');
            } else {
                $review_error = "Error al actualizar la reseña. Por favor, inténtelo de nuevo.";
            }
            $stmt->close();
        } else {
            $review_error = "Error al preparar la consulta de actualización.";
        }
    }
}

$conn->close();
?>