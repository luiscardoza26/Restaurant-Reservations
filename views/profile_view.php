<?php require_once '../profile.php'; ?>
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
                    <?php if (isRestaurantAdmin() || isAdmin()): ?>
                    <a class="nav-link" href="index_view_admin.php">Inicio</a>
                    <a class="nav-link" href="restaurants_view_admin.php">Panel de Administrador</a>
                    <?php else: ?>
                        <a class="nav-link" href="index_view.php">Inicio</a>
                        <a class="nav-link" href="search_view.php">Buscar</a>
                    <?php endif; ?>
                    <a class="nav-link" href="../logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <section class="user-info mb-4">
            <h3 class="title-general">Información del Usuario</h3>
            <p><strong>Nombre de usuario:</strong> <?php echo e($username); ?></p>
            <p><strong>Correo electrónico:</strong> <?php echo e($email); ?></p>
            <?php if (isRestaurantAdmin()): ?>
                <p><strong>NIT:</strong> <?php echo e($nit); ?></p>
            <?php endif; ?>
            <a href="change_password_view.php" class="btn btn-general">Cambiar contraseña</a>
            <a href="../delete_account.php" class="btn btn-cancel" onclick="return confirm('¿Está seguro de que desea eliminar su cuenta?');">Eliminar cuenta</a>
        </section>

        <hr>
        
        <?php if (isLoggedIn() && !isAdmin() && !isRestaurantAdmin()): ?>
            <section class="user-reservations mb-4">
                <h3 class="title-general">Mis Reservas</h3>
                <?php if (!empty($reservations)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hove">
                            <thead>
                                <tr>
                                    <th scope="col">Restaurante</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Hora</th>
                                    <th scope="col">Número de personas</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td><?php echo e($reservation['restaurant_name']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($reservation['reservation_date'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($reservation['reservation_time'])); ?></td>
                                        <td><?php echo $reservation['party_size']; ?></td>
                                        <td><?php echo $reservation['status']; ?></td>
                                        <td>
                                            <a href="edit_reservation_view.php?id=<?php echo $reservation['id']; ?>" class="btn edit-general-btn"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <a href="../cancel_reservation.php?id=<?php echo $reservation['id']; ?>" onclick="return confirm('¿Está seguro de que desea cancelar esta reserva?');" class="btn btn-cancel"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-corto-general">No tienes reservas activas.</p>
                <?php endif; ?>
            </section>

            <section class="user-reviews">
                <h3 class="title-general">Mis Reseñas</h3>
                <?php if (!empty($reviews)): ?>
                    <ul class="review-list">
                        <?php foreach ($reviews as $review): ?>
                            <h4 class="title-general"><?php echo e($review['restaurant_name']); ?></h4>
                            <p>Calificación: <?php echo $review['rating']; ?>/5</p>
                            <p><?php echo e($review['comment']); ?></p>
                            <p class="review-date">Fecha: <?php echo date('d/m/Y', strtotime($review['created_at'])); ?></p>
                            <a href="edit_review_view.php?id=<?php echo $review['id']; ?>" class="btn btn-general">Editar reseña</a>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-corto-general">No has dejado ninguna reseña aún.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>