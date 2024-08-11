<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

// Verificar si el usuario ha iniciado sesión y es un administrador
if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('login_view.php');
}

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$conn = getDBConnection();

// Obtener la información de la reserva
$sql = "SELECT r.*, u.username, res.name as restaurant_name
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN restaurants res ON r.restaurant_id = res.id
        WHERE r.id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $reservation_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($reservation = $result->fetch_assoc()) {
            // La reserva existe
        } else {
            // La reserva no existe
            $_SESSION['error_message'] = "La reserva no existe.";
            redirectTo('reservations_view_admin.php');
        }
    } else {
        echo "Oops! Algo salió mal al obtener la reserva. Por favor, inténtelo de nuevo más tarde.";
    }
    $stmt->close();
} else {
    echo "Oops! Algo salió mal al preparar la consulta. Por favor, inténtelo de nuevo más tarde.";
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("Error de validación CSRF.");
    }

    $fields_to_update = [];
    $types = "";
    $params = [];

    // Validar y preparar los campos que se van a actualizar
    if (!empty($_POST["reservation_date"]) && $_POST["reservation_date"] != $reservation['reservation_date']) {
        list($date_valid, $date_result) = validateAndSanitize($_POST["reservation_date"], 'date');
        if ($date_valid) {
            $fields_to_update[] = "reservation_date = ?";
            $types .= "s";
            $params[] = $date_result;
        }
    }

    if (!empty($_POST["reservation_time"]) && $_POST["reservation_time"] != $reservation['reservation_time']) {
        list($time_valid, $time_result) = validateAndSanitize($_POST["reservation_time"], 'time');
        if ($time_valid) {
            $fields_to_update[] = "reservation_time = ?";
            $types .= "s";
            $params[] = $time_result;
        }
    }

    if (!empty($_POST["party_size"]) && $_POST["party_size"] != $reservation['party_size']) {
        list($size_valid, $size_result) = validateAndSanitize($_POST["party_size"], 'number');
        if ($size_valid) {
            $fields_to_update[] = "party_size = ?";
            $types .= "i";
            $params[] = $size_result;
        }
    }

    // Manejar el campo status
    if (isset($_POST["status"]) && $_POST["status"] !== $reservation['status']) {
        $status_result = trim($_POST["status"]);
        // Validar que el status sea uno de los valores permitidos
        $allowed_statuses = ['pending', 'confirmed', 'cancelled'];
        if (in_array($status_result, $allowed_statuses)) {
            $fields_to_update[] = "status = ?";
            $types .= "s";
            $params[] = $status_result;
        } else {
            $_SESSION['error_message'] = "Estado de reserva no válido.";
        }
    }

    // Si hay campos para actualizar, proceder con la actualización
    if (!empty($fields_to_update)) {
        $sql = "UPDATE reservations SET " . implode(", ", $fields_to_update) . " WHERE id = ?";
        $types .= "i";
        $params[] = $reservation_id;

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param($types, ...$params);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "La reserva ha sido actualizada exitosamente.";
                redirectTo('reservations_view_admin.php');
            } else {
                echo "Oops! Algo salió mal al actualizar la reserva. Error: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Oops! Algo salió mal al preparar la consulta de actualización. Error: " . $conn->error;
        }
    } else {
        $_SESSION['info_message'] = "No se realizaron cambios en la reserva.";
        redirectTo('reservations_view_admin.php');
    }
}

$conn->close();
?>

