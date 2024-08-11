<?php require_once '../restaurant.php'; ?>
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
                    <a class="nav-link" href="search_view.php">Buscar</a>
                    <?php if (isLoggedIn()): ?>
                        <a class="nav-link" href="../logout.php">Cerrar sesión</a>
                    <?php else: ?>
                        <a class="nav-link" href="login_view.php">Iniciar sesión</a>
                        <a class="nav-link" href="register_view.php">Registrarse</a>
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
            <a href="reservation_view.php?restaurant_id=<?php echo $restaurant['id']; ?>" class="btn btn-general">Hacer una reserva</a>
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
<?php include_once '../includes/plantillas/footer/footer.php'; ?>