<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

function hasRole($role) {
    return isLoggedIn() && $_SESSION['role'] === $role;
}

function isAdmin() {
    // return hasRole('system_admin');
    return isset($_SESSION['role']) && $_SESSION['role'] === 'system_admin';
}

function isRestaurantAdmin() {
    // return hasRole('restaurant_admin');
    return isset($_SESSION['role']) && $_SESSION['role'] === 'restaurant_admin';
}

function redirectTo($location) {
    header("Location: $location");
    exit;
}

function validateAndSanitize($data, $field) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    
    switch ($field) {
        case 'username':
            if (empty($data)) {
                return [false, "El nombre de usuario es requerido."];
            } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $data)) {
                return [false, "El nombre de usuario solo puede contener letras, números y guiones bajos."];
            }
            break;
        case 'email':
            if (empty($data)) {
                return [false, "El correo electrónico es requerido."];
            } elseif (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
                return [false, "Formato de correo electrónico inválido."];
            }
            break;
        case 'password':
            if (empty($data)) {
                return [false, "La contraseña es requerida."];
            } elseif (strlen($data) < 6) {
                return [false, "La contraseña debe tener al menos 6 caracteres."];
            }
            break;
        case 'name':
            if (empty($data)) {
                return [false, "El nombre es requerido."];
            } elseif (!preg_match("/^[a-zA-Z0-9\s]+$/", $data)) {
                return [false, "El nombre solo puede contener letras, números y espacios."];
            }
            break;
        case 'phone':
            if (empty($data)) {
                return [false, "El teléfono es requerido."];
            } elseif (!preg_match("/^[0-9\-\(\)\/\+\s]*$/", $data)) {
                return [false, "Formato de teléfono inválido."];
            }
            break;
        case 'date':
            if (empty($data)) {
                return [false, "La fecha es requerida."];
            } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $data)) {
                return [false, "Formato de fecha inválido. Use YYYY-MM-DD."];
            }
            break;
        case 'time':
            if (empty($data)) {
                return [false, "La hora es requerida."];
            } elseif (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $data)) {
                return [false, "Formato de hora inválido. Use HH:MM."];
            }
            break;
        case 'number':
            if (empty($data)) {
                return [false, "El número es requerido."];
            } elseif (!is_numeric($data)) {
                return [false, "Debe ser un número válido."];
            }
            break;
    }
    
    return [true, $data];
}

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

?>
