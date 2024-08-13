<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/security_headers.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('../login.php');
}

$conn = getDBConnection();

// Obtener todas las reservas
$sql = "SELECT r.id, u.username, res.name as restaurant_name, r.reservation_date, r.reservation_time, r.party_size, r.status 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        JOIN restaurants res ON r.restaurant_id = res.id 
        ORDER BY r.reservation_date DESC, r.reservation_time DESC";
$result = $conn->query($sql);
$reservations = $result->fetch_all(MYSQLI_ASSOC);

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
        <h2 class="title-general">Gestionar Reservas</h2>
        <?php if (!empty($reservations)): ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Usuario</th>
                    <th scope="col">Restaurante</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Hora</th>
                    <th scope="col">Personas</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?php echo $reservation['id']; ?></td>
                    <td><?php echo $reservation['username']; ?></td>
                    <td><?php echo $reservation['restaurant_name']; ?></td>
                    <td><?php echo $reservation['reservation_date']; ?></td>
                    <td><?php echo $reservation['reservation_time']; ?></td>
                    <td><?php echo $reservation['party_size']; ?></td>
                    <td><?php echo $reservation['status']; ?></td>
                    <td>
                        <a href="edit_reservations.php?id=<?php echo $reservation['id']; ?>" class="btn edit-general-btn"><i class="fa-regular fa-pen-to-square"></i></a>
                        <a href="delete_reservations.php?id=<?php echo $reservation['id']; ?>" class="btn btn-cancel"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="texto-corto-general">No hay reservas disponibles.</p>
        <?php endif; ?>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>


