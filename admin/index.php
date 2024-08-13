<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';
require_once '../includes/security_headers.php';

// Verificar si el usuario está logueado
if (isAdmin() || isRestaurantAdmin()) {
    $user_id = $_SESSION['id'];
    $username = $_SESSION['username'];
} else {
    $user_id = null;
    $username = null;
}

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
                    <a class="nav-link" href="restaurants.php">Panel de Administrador</a>
                    <a class="nav-link" href="../profile.php">Mi Perfil</a>
                    <a class="nav-link" href="../logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <?php if (isAdmin() || isRestaurantAdmin() && $user_id): ?>
            <h2 class="title-general">Bienvenido <?php echo $username ?></h2>
        <?php endif; ?>
        <h6>Seleccione una opción del menú para gestionar el sistema.</h6>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>


