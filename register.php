<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';


$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = $nit_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    $conn = getDBConnection();

    $role = $_POST['role'];
    $nit = null;
    
    // Validar username
    list($username_valid, $username_result) = validateAndSanitize($_POST["username"], 'username');
    if (!$username_valid) {
        $username_err = $username_result;
    } else {
        $username = $username_result;
        // Verificar si el username ya existe en la base de datos
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "Este nombre de usuario ya está en uso.";
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    // Validar email
    list($email_valid, $email_result) = validateAndSanitize($_POST["email"], 'email');
    if (!$email_valid) {
        $email_err = $email_result;
    } else {
        $email = $email_result;
        // Verificar si el email ya existe en la base de datos
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "Este correo electrónico ya está en uso.";
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    // Validar password
    list($password_valid, $password_result) = validateAndSanitize($_POST["password"], 'password');
    if (!$password_valid) {
        $password_err = $password_result;
    } else {
        $password = $password_result;
    }
    
    // Validar confirm_password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor confirme la contraseña.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }

    // Validar NIT basado en el rol
    if ($role === 'restaurant_admin') {
        $nit = trim($_POST['nit']);
        
        // Validación del NIT
        if (empty($nit)) {
            $nit_err = "Por favor ingrese el NIT.";
        } elseif (!preg_match('/^\d{9}$/', $nit)) {
            $nit_err = "El NIT debe contener exactamente 9 dígitos.";
        }
    } elseif ($role !== 'user') {
        $role_err = "Rol no válido.";
    }
    
    // Verificar errores de entrada antes de insertar en la base de datos
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($nit_err)) {
        $sql = "INSERT INTO users (username, email, password, role, nit) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $param_username, $param_email, $param_password, $param_role, $param_nit);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Crea un hash de la contraseña
            $param_role = $role;
            $param_nit = $nit;
            
            if ($stmt->execute()) {
                redirectTo('login_view.php');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>