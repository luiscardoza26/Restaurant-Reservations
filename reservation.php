<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';

if (!isLoggedIn()) {
    redirectTo('login_view.php');
}

$conn = getDBConnection();

$restaurant_id = isset($_GET['restaurant_id']) ? intval($_GET['restaurant_id']) : 0;
$reservation_date = $reservation_time = $party_size = "";
$reservation_date_err = $reservation_time_err = $party_size_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    // Validar fecha de reserva
    list($date_valid, $date_result) = validateAndSanitize($_POST["reservation_date"], 'date');
    if (!$date_valid) {
        $reservation_date_err = $date_result;
    } else {
        $reservation_date = $date_result;
    }
    
    // Validar hora de reserva
    list($time_valid, $time_result) = validateAndSanitize($_POST["reservation_time"], 'time');
    if (!$time_valid) {
        $reservation_time_err = $time_result;
    } else {
        $reservation_time = $time_result;
    }
    
    // Validar tamaño del grupo
    list($size_valid, $size_result) = validateAndSanitize($_POST["party_size"], 'number');
    if (!$size_valid) {
        $party_size_err = $size_result;
    } else {
        $party_size = $size_result;
    }
    
    // Verificar errores de entrada antes de insertar en la base de datos
    if (empty($reservation_date_err) && empty($reservation_time_err) && empty($party_size_err)) {
        $sql = "INSERT INTO reservations (user_id, restaurant_id, reservation_date, reservation_time, party_size) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iissi", $param_user_id, $param_restaurant_id, $param_reservation_date, $param_reservation_time, $param_party_size);
            $param_user_id = $_SESSION["id"];
            $param_restaurant_id = $restaurant_id;
            $param_reservation_date = $reservation_date;
            $param_reservation_time = $reservation_time;
            $param_party_size = $party_size;
            
            if ($stmt->execute()) {
                redirectTo('index_view.php?reservation_success=1');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
}

// Obtener información del restaurante
$restaurant_name = "";
if ($restaurant_id > 0) {
    $sql = "SELECT name FROM restaurants WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $restaurant_id);
        if ($stmt->execute()) {
            $stmt->bind_result($restaurant_name);
            $stmt->fetch();
        }
        $stmt->close();
    }
}

$conn->close();
?>