<?php require_once '../admin/index.php'; ?>
<?php require_once '../includes/security_headers.php'; ?>
<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="restaurants_view_admin.php">Panel de Administrador</a>
                    <a class="nav-link" href="profile_view.php">Mi Perfil</a>
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