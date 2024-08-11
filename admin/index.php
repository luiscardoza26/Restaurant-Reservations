<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

// Verificar si el usuario está logueado
if (isAdmin() || isRestaurantAdmin()) {
    $user_id = $_SESSION['id'];
    $username = $_SESSION['username'];
} else {
    $user_id = null;
    $username = null;
}

?>


