<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security_headers.php';

// Verificar si el usuario está logueado
if (isLoggedIn()) {
    $user_id = $_SESSION['id'];
    $username = $_SESSION['username'];
} else {
    $user_id = null;
    $username = null;
}

$conn = getDBConnection();

// Obtener lista de restaurantes
$sql = "SELECT * FROM restaurants";
$result = $conn->query($sql);
$restaurants = $result->fetch_all(MYSQLI_ASSOC);

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
                    <a class="nav-link" href="index.php">Inicio</a>
                    <a class="nav-link" href="search.php">Buscar</a>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isRestaurantAdmin() || isAdmin()): ?>
                            <a class="nav-link" href="./admin/restaurants.php">Panel de Administración</a>
                        <?php endif; ?>
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

        <?php if (isLoggedIn() && $user_id): ?>
            <h3 class="title-wellcome">Bienvenido <?php echo $username ?></h3>
        <?php endif; ?>

        <h3 class="title-general">Restaurantes disponibles</h3>
        <ul class="restaurant-list">
            <?php foreach ($restaurants as $restaurant): ?>
                <h3 class="title-general"><?php echo $restaurant['name']; ?></h3>
                <p>Dirección: <?php echo $restaurant['address']; ?></p>
                <p>Teléfono: <?php echo $restaurant['phone']; ?></p>
                <p>Cocina: <?php echo $restaurant['cuisine_type']; ?></p>
                <?php if (isLoggedIn()): ?>
                    <a href="reservation.php?restaurant_id=<?php echo $restaurant['id']; ?>" class="btn btn-general mb-3">Reservar</a>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php include_once './includes/plantillas/footer/footer.php'; ?>