<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security_headers.php';

// Verificar si el usuario ha iniciado sesión
if (!isLoggedIn()) {
    redirectTo('login.php');
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
            redirectTo('profile.php');
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
                redirectTo('profile.php');
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


<?php include_once './includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <?php if (isRestaurantAdmin() || isAdmin()): ?>
                    <a class="nav-link" href="./admin/index.php">Inicio</a>
                    <?php else: ?>
                        <a class="nav-link" href="index.php">Inicio</a>
                    <?php endif; ?>
                    <a class="nav-link" href="profile.php">Mi Perfil</a>
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="card border-0 card-general">
            <div class="card-body">
                <h2 class="title-general">Editar Reseña para <?php echo e($review['restaurant_name']); ?></h2>
        
                <?php if (isset($review_error)): ?>
                    <p class="text_corto_general"><?php echo $review_error; ?></p>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $review_id); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="rating">Calificación:</label>
                        <select class="form-control" id="rating" name="rating" required>
                            <option value="">Selecciona una calificación</option>
                            <option value="1" <?php echo ($review['rating'] == 1) ? 'selected' : ''; ?>>1 - Malo</option>
                            <option value="2" <?php echo ($review['rating'] == 2) ? 'selected' : ''; ?>>2 - Regular</option>
                            <option value="3" <?php echo ($review['rating'] == 3) ? 'selected' : ''; ?>>3 - Bueno</option>
                            <option value="4" <?php echo ($review['rating'] == 4) ? 'selected' : ''; ?>>4 - Muy bueno</option>
                            <option value="5" <?php echo ($review['rating'] == 5) ? 'selected' : ''; ?>>5 - Excelente</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="comment">Comentario:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" cols="50" required><?php echo e($review['comment']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-general" type="submit" value="Actualizar reseña">
                        <a href="profile_view.php" class="btn btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once './includes/plantillas/footer/footer.php'; ?>