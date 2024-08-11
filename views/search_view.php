<?php require_once '../search.php'; ?>
<?php require_once '../includes/security_headers.php'; ?>
<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="index_view.php">Inicio</a>
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
        <h2 class="title-general">Buscar Restaurantes</h2>
        <div class="card border-0">
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                    <div class="mb-3">
                        <label for="query" class="form-label">Buscar por nombre o tipo de cocina:</label>
                        <input class="form-control" type="text" id="query" name="query" value="<?php echo e($search_query); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Ubicación (opcional):</label>
                        <input class="form-control" type="text" id="location" name="location" value="<?php echo e($search_location); ?>">
                    </div>
                    <div>
                        <input class="btn btn-general" type="submit" value="Buscar">
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($restaurants)): ?>
            <ul class="restaurant-list">
                <?php foreach ($restaurants as $restaurant): $avg_rating = number_format($restaurant['avg_rating'], 1); ?>
                        <h4 class="title-general"><?php echo e($restaurant['name']); ?></h4>
                        <p>Dirección: <?php echo e($restaurant['address']); ?></p>
                        <p>Teléfono: <?php echo e($restaurant['phone']); ?></p>
                        <p>Calificación promedio: <?php echo $avg_rating; ?> / 5</p>
                        <a class="nav-link" id="detalle" href="restaurant_view.php?id=<?php echo $restaurant['id']; ?>">Ver detalles</a>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])): ?>
            <p class="text-corto-general">No se encontraron resultados para su búsqueda.</p>
        <?php endif; ?>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>