<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('login_view.php');
}

$name = $address = $phone = "";
$name_err = $address_err = $phone_err = "";
$cuisine_type = "";
$cuisine_type_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);
    
    $conn = getDBConnection();
    
    // Validar nombre
    list($name_valid, $name_result) = validateAndSanitize($_POST["name"], 'name');
    if (!$name_valid) {
        $name_err = $name_result;
    } else {
        $name = $name_result;
    }
    
    // Validar dirección
    list($address_valid, $address_result) = validateAndSanitize($_POST["address"], 'name');
    if (!$address_valid) {
        $address_err = $address_result;
    } else {
        $address = $address_result;
    }
    
    // Validar teléfono
    list($phone_valid, $phone_result) = validateAndSanitize($_POST["phone"], 'phone');
    if (!$phone_valid) {
        $phone_err = $phone_result;
    } else {
        $phone = $phone_result;
    }

    // Validar tipo de cocina
    list($cuisine_type_valid, $cuisine_type_result) = validateAndSanitize($_POST["cuisine_type"], 'name');
    if (!$cuisine_type_valid) {
        $cuisine_type_err = $cuisine_type_result;
    } else {
        $cuisine_type = $cuisine_type_result;
    }
    
    // Verificar errores de entrada antes de insertar en la base de datos
    if (empty($name_err) && empty($address_err) && empty($phone_err)) {
        $sql = "INSERT INTO restaurants (name, address, phone, cuisine_type) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $param_name, $param_address, $param_phone, $param_cusine_type);
            $param_name = $name;
            $param_address = $address;
            $param_phone = $phone;
            $param_cusine_type = $cuisine_type;
            
            if ($stmt->execute()) {
                redirectTo('restaurants_view_admin.php');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>
