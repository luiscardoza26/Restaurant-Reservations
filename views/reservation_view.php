<?php require_once '../reservation.php'; ?>
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
                    <a class="nav-link" href="index_view.php">Inicio</a>
                    <a class="nav-link" href="profile_view.php">Mi Perfil</a>
                    <a class="nav-link" href="../logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="card border-0 card-general">
            <div class="card-body">
                <h3 class="title-general">Haz tu Reserva</h3>
                <?php
                if (!empty($restaurant_name)) {
                    echo "<h5 class='title-general'>Restaurante: $restaurant_name</h5>";
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?restaurant_id=" . $restaurant_id); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="reservation_date">Fecha de reserva:</label>
                        <input class="form-control" type="date" id="reservation_date" name="reservation_date" value="<?php echo $reservation_date; ?>">
                        <span class="text-corto-general"><?php echo $reservation_date_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reservation_time">Hora de reserva:</label>
                        <input class="form-control" type="time" id="reservation_time" name="reservation_time" value="<?php echo $reservation_time; ?>">
                        <span class="text-corto-general"><?php echo $reservation_time_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="party_size">Número de personas:</label>
                        <input class="form-control" type="number" id="party_size" name="party_size" min="1" value="<?php echo $party_size; ?>">
                        <span class="text-corto-general"><?php echo $party_size_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-general" type="submit" value="Reservar">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>