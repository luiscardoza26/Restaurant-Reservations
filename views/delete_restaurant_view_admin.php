<?php require_once '../admin/delete_restaurant.php'; ?>
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
                <h3 class="title-general">Eliminar Restaurante</h3>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input class="form-control" type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>">
                    <p>¿Está seguro de que desea eliminar este restaurante?</p>
                    <div>
                        <input class="btn btn-general" type="submit" value="Sí">
                        <a href="restaurants_view_admin.php" class="btn btn-cancel">No</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>