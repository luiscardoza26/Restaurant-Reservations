<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';


$restaurant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$restaurant = null;

if ($restaurant_id > 0) {
    $conn = getDBConnection();
    $sql = "SELECT * FROM restaurants WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $restaurant_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $restaurant = $result->fetch_assoc();
        }
        $stmt->close();
    }
    $conn->close();
}

// Obtener reseñas del restaurante
$reviews = [];
$avg_rating = 0;
$conn = getDBConnection();
$sql = "SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.restaurant_id = ? ORDER BY r.created_at DESC";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $restaurant_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
    }
    $stmt->close();
}

// Calcular calificación promedio
if (!empty($reviews)) {
    $total_rating = array_sum(array_column($reviews, 'rating'));
    $avg_rating = round($total_rating / count($reviews), 1);
}

// Procesar el envío de una nueva reseña
if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn()) {
    validateCSRFToken($_POST['csrf_token']);

    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = trim($_POST['comment']);

    // Verificar si el usuario ya ha dejado una reseña para este restaurante
    $sql = "SELECT id FROM reviews WHERE user_id = ? AND restaurant_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $_SESSION['id'], $restaurant_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $review_error = "Ya has dejado una reseña para este restaurante.";
        } else {
            // Proceder con la inserción de la nueva reseña
            if ($rating >= 1 && $rating <= 5) {
                $sql = "INSERT INTO reviews (user_id, restaurant_id, rating, comment) VALUES (?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("iiis", $_SESSION['id'], $restaurant_id, $rating, $comment);
                    if ($stmt->execute()) {
                        // Recargar la página para mostrar la nueva reseña
                        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $restaurant_id);
                        exit();
                    } else {
                        $review_error = "Error al enviar la reseña. Por favor, inténtelo de nuevo.";
                    }
                    $stmt->close();
                }
            } else {
                $review_error = "Por favor, seleccione una calificación válida.";
            }
        }
        $stmt->close();
    }
}

$conn->close();

if (!$restaurant) {
    redirectTo('index_view.php');
}
?>

