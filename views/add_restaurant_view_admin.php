<?php require_once '../admin/add_restaurant.php'; ?>
<?php require_once '../includes/security_headers.php'; ?>
<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
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
                <h2 class="title-general">Añadir Restaurante</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="name">Nombre:</label>
                        <input class="form-control" type="text" id="name" name="name" value="<?php echo $name; ?>">
                        <span class="text-corto-general"><?php echo $name_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="address">Dirección:</label>
                        <input class="form-control" type="text" id="address" name="address" value="<?php echo $address; ?>">
                        <span class="text-corto-general"><?php echo $address_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone">Teléfono:</label>
                        <input class="form-control" type="text" id="phone" name="phone" value="<?php echo $phone; ?>">
                        <span class="text-corto-general"><?php echo $phone_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="cuisine_type">Tipo de cocina:</label>
                        <input class="form-control" type="text" id="cuisine_type" name="cuisine_type" value="<?php echo e($cuisine_type); ?>">
                        <span class="text-corto-general"><?php echo $cuisine_type_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-general" type="submit" value="Añadir Restaurante">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>