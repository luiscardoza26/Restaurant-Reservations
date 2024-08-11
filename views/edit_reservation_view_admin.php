<?php require_once '../admin/edit_reservations.php'; ?>
<?php require_once '../includes/security_headers.php'; ?>
<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index_view_admin.php">Inicio</a>
                    <a class="nav-link" href="restaurants_view_admin.php">Panel de Administrador</a>
                    <a class="nav-link" href="profile_view.php">Mi Perfil</a>
                    <a class="nav-link" href="../logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="card border-0 card-general">
            <div class="card-body">
                <h2 class="title-general">Editar Reserva</h2>
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo "<p class='text-corto-general'>" . $_SESSION['error_message'] . "</p>";
                    unset($_SESSION['error_message']);
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $reservation_id); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="username">Usuario:</label>
                        <input class="form-control" type="text" id="username" name="username" value="<?php echo e($reservation['username']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="restaurant_name">Restaurante:</label>
                        <input class="form-control" type="text" id="restaurant_name" name="restaurant_name" value="<?php echo e($reservation['restaurant_name']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reservation_date">Fecha de reserva:</label>
                        <input class="form-control" type="date" id="reservation_date" name="reservation_date" value="<?php echo $reservation['reservation_date']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reservation_time">Hora de reserva:</label>
                        <input class="form-control" type="time" id="reservation_time" name="reservation_time" value="<?php echo $reservation['reservation_time']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="party_size">Número de personas:</label>
                        <input class="form-control" type="number" id="party_size" name="party_size" value="<?php echo $reservation['party_size']; ?>" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="status">Status:</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending" <?php echo ($reservation['status'] == 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="confirmed" <?php echo ($reservation['status'] == 'confirmed') ? 'selected' : ''; ?>>Aprobada</option>
                            <option value="cancelled" <?php echo ($reservation['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="submit" value="Actualizar Reserva" class="btn btn-general">
                        <a href="reservations_view_admin.php" class="btn btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>