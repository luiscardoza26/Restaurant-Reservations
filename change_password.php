<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';

// Verificar si el usuario ha iniciado sesión
if (!isLoggedIn()) {
    redirectTo('login_view.php');
}

$user_id = $_SESSION['id'];
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("Error de validación CSRF.");
    }

    // Validar contraseña actual
    if (empty(trim($_POST["current_password"]))) {
        $current_password_err = "Por favor, ingrese su contraseña actual.";
    } else {
        $current_password = trim($_POST["current_password"]);
    }

    // Validar nueva contraseña
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Por favor, ingrese la nueva contraseña.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validar confirmación de contraseña
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor, confirme la contraseña.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }

    // Verificar errores de entrada antes de actualizar la base de datos
    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        $conn = getDBConnection();

        // Verificar la contraseña actual
        $sql = "SELECT password FROM users WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($current_password, $hashed_password)) {
                            // La contraseña actual es correcta, actualizar la nueva contraseña
                            $sql = "UPDATE users SET password = ? WHERE id = ?";
                            if ($update_stmt = $conn->prepare($sql)) {
                                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                                $update_stmt->bind_param("si", $new_hashed_password, $user_id);
                                if ($update_stmt->execute()) {
                                    // Contraseña actualizada exitosamente
                                    $success_message = "Su contraseña ha sido actualizada exitosamente.";
                                    // Limpiar las variables
                                    $current_password = $new_password = $confirm_password = "";
                                } else {
                                    echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
                                }
                                $update_stmt->close();
                            }
                        } else {
                            $current_password_err = "La contraseña actual es incorrecta.";
                        }
                    }
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
