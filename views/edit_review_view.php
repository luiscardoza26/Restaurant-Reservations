<?php require_once '../edit_review.php'; ?>
    <?php require_once '../includes/security_headers.php'; ?>
    <?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <?php if (isRestaurantAdmin() || isAdmin()): ?>
                    <a class="nav-link" href="index_view_admin.php">Inicio</a>
                    <?php else: ?>
                        <a class="nav-link" href="index_view.php">Inicio</a>
                    <?php endif; ?>
                    <a class="nav-link" href="profile_view.php">Mi Perfil</a>
                    <a class="nav-link" href="../logout.php">Cerrar sesión</a>
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
<?php include_once '../includes/plantillas/footer/footer.php'; ?>