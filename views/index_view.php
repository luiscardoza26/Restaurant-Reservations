<?php require_once '../index.php'; ?>
<?php require_once '../includes/security_headers.php'; ?>
<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="index_view.php">Inicio</a>
                    <a class="nav-link" href="search_view.php">Buscar</a>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isRestaurantAdmin() || isAdmin()): ?>
                            <a class="nav-link" href="restaurants_view_admin.php">Panel de Administración</a>
                        <?php endif; ?>
                        <a class="nav-link" href="profile_view.php">Mi Perfil</a>
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

        <?php if (isLoggedIn() && $user_id): ?>
            <h3 class="title-wellcome">Bienvenido <?php echo $username ?></h3>
        <?php endif; ?>

        <h3 class="title-general">Restaurantes disponibles</h3>
        <ul class="restaurant-list">
            <?php foreach ($restaurants as $restaurant): ?>
                <h3 class="title-general"><?php echo $restaurant['name']; ?></h3>
                <p>Dirección: <?php echo $restaurant['address']; ?></p>
                <p>Teléfono: <?php echo $restaurant['phone']; ?></p>
                <p>Cocina: <?php echo $restaurant['cuisine_type']; ?></p>
                <?php if (isLoggedIn()): ?>
                    <a href="reservation_view.php?restaurant_id=<?php echo $restaurant['id']; ?>" class="btn btn-general mb-3">Reservar</a>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>