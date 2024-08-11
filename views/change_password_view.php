<?php require_once '../change_password.php'; ?>
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
                <h4 class="title-general">Cambiar Contraseña</h4>
                <?php
                if (!empty($success_message)) {
                    echo "<p class='title-general'>" . $success_message . "</p>";
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="current_password">Contraseña actual:</label>
                        <input class="form-control" type="password" id="current_password" name="current_password" value="<?php echo $current_password; ?>">
                        <span class="text-corto-general"><?php echo $current_password_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="new_password">Nueva contraseña:</label>
                        <input class="form-control" type="password" id="new_password" name="new_password" value="<?php echo $new_password; ?>">
                        <span class="text-corto-general"><?php echo $new_password_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="confirm_password">Confirmar nueva contraseña:</label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                        <span class="text-corto-general"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-general" type="submit" value="Cambiar Contraseña">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>