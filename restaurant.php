<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security_headers.php';


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
    redirectTo('index.php');
}
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
                    <a class="nav-link" href="index.php">Inicio</a>
                    <a class="nav-link" href="search.php">Buscar</a>
                    <?php if (isLoggedIn()): ?>
                        <a class="nav-link" href="profile.php">Mi Perfil</a>
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">Iniciar sesión</a>
                        <a class="nav-link" href="register.php">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <h3 class="title-general"><?php echo e($restaurant['name']); ?></h3>
        <p>Tipo de cocina: <?php echo e($restaurant['cuisine_type']); ?></p>
        <p>Dirección: <?php echo e($restaurant['address']); ?></p>
        <p>Teléfono: <?php echo e($restaurant['phone']); ?></p>
        
        <?php if (isLoggedIn()): ?>
            <a href="reservation.php?restaurant_id=<?php echo $restaurant['id']; ?>" class="btn btn-general">Hacer una reserva</a>
        <?php else: ?>
            <p class="text-corto-general">Inicia sesión para hacer una reserva.</p>
        <?php endif; ?>
        
        <hr>

        <section class="reviews">
            <h4 class="title-general">Reseñas</h4>
            <p>Calificación promedio: <?php echo $avg_rating; ?> / 5</p>
            
            <?php if (isLoggedIn()): ?>
                <h5 class="title-general">Deja tu reseña</h5>
                <?php if (isset($review_error)): ?>
                    <p class="text-corto-general"><?php echo $review_error; ?></p>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $restaurant_id); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="rating">Calificación:</label>
                        <select class="form-control" id="rating" name="rating" required>
                            <option value="">Selecciona una calificación</option>
                            <option value="1">1 - Malo</option>
                            <option value="2">2 - Regular</option>
                            <option value="3">3 - Bueno</option>
                            <option value="4">4 - Muy bueno</option>
                            <option value="5">5 - Excelente</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"  for="comment">Comentario:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" cols="50"></textarea>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-general" type="submit" value="Enviar reseña">
                    </div>
                </form>
            <?php else: ?>
                <p class="text-corto-general">Inicia sesión para dejar una reseña.</p>
            <?php endif; ?>
            
            <hr>
            
            <?php if (!empty($reviews)): ?>
                <h5 class="title-general">Comentarios</h5>
                <?php foreach ($reviews as $review): ?>
                    <p><strong><?php echo e($review['username']); ?></strong> - Calificación: <?php echo $review['rating']; ?>/5</p>
                    <p><?php echo e($review['comment']); ?></p>
                    <p class="review-date">Fecha: <?php echo date('d/m/Y', strtotime($review['created_at'])); ?></p>
                <?php endforeach; ?>

            <?php else: ?>
                <p class="text-corto-general">Aún no hay reseñas para este restaurante.</p>
            <?php endif; ?>
        </section>
    </div>
<?php include_once './includes/plantillas/footer/footer.php'; 

