<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('login_view.php');
}

$conn = getDBConnection();

$id = $name = $address = $phone = "";
$name_err = $address_err = $phone_err = "";
$cuisine_type = "";
$cuisine_type_err = "";

// Procesar datos del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    // Validar ID
    if (empty(trim($_POST["id"]))) {
        redirectTo('restaurants_view_admin.php');
    } else {
        $id = trim($_POST["id"]);
    }
    
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
    
    // Verificar errores de entrada antes de actualizar en la base de datos
    if (empty($name_err) && empty($address_err) && empty($phone_err) && empty($cuisine_type_err)) {
        $sql = "UPDATE restaurants SET name = ?, address = ?, phone = ?, cuisine_type = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $param_name, $param_address, $param_phone, $param_cuisine_type, $param_id);
            $param_name = $name;
            $param_address = $address;
            $param_phone = $phone;
            $param_cuisine_type = $cuisine_type;
            $param_id = $id;
            
            if ($stmt->execute()) {
                redirectTo('restaurants_view_admin.php');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
} else {
    // Verificar si se proporcionó un ID válido
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        
        $sql = "SELECT * FROM restaurants WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $param_id);
            $param_id = $id;
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    $name = $row["name"];
                    $address = $row["address"];
                    $phone = $row["phone"];
                    $cuisine_type = $row["cuisine_type"];
                } else {
                    redirectTo('restaurants_view_admin.php');
                    exit();
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
        }
        $stmt->close();
    } else {
        redirectTo('restaurants_view_admin.php');
        exit();
    }
}

$conn->close();
?>

