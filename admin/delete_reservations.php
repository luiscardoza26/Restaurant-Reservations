<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';
require_once '../includes/security_headers.php';

// Verificar si el usuario es administrador
if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('../login.php');
}

$conn = getDBConnection();

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Procesar la solicitud de eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    $sql = "DELETE FROM reservations WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $reservation_id);
        
        if ($stmt->execute()) {
            redirectTo('reservations.php');
            exit();
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }
    }
    $stmt->close();
}

$conn->close();
?>


<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index.php">Inicio</a>
                    <a class="nav-link" href="restaurants.php">Panel de Administrador</a>
                    <a class="nav-link" href="../profile.php">Mi Perfil</a>
                    <a class="nav-link" href="../logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="card border-0 card-general">
            <div class="card-body">
                <h3 class="title-general">Eliminar Reserva</h3>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $reservation_id); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <p>¿Está seguro de que desea eliminar esta reserva?</p>
                    <div>
                        <input class="btn btn-general" type="submit" value="Sí">
                        <a href="reservations.php" class="btn btn-cancel">No</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>