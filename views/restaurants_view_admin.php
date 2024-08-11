<?php require_once '../admin/restaurants.php'; ?>
<?php require_once '../includes/security_headers.php'; ?>
<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="index_view_admin.php">Inicio</a>
                    <a class="nav-link" href="reservations_view_admin.php">Gestionar Reservas</a>
                    <a class="nav-link" href="profile_view.php">Mi Perfil</a>
                    <a class="nav-link" href="../logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <h2 class="title-general">Gestionar Restaurantes</h2>
        <a href="add_restaurant_view_admin.php" class="btn btn-general mb-3">Añadir Restaurante</a>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Dirección</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Cocina</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                <tr>
                    <td><?php echo $restaurant['id']; ?></td>
                    <td><?php echo $restaurant['name']; ?></td>
                    <td><?php echo $restaurant['address']; ?></td>
                    <td><?php echo $restaurant['phone']; ?></td>
                    <td><?php echo $restaurant['cuisine_type']; ?></td>
                    <td>
                        <a href="edit_restaurant_view_admin.php?id=<?php echo $restaurant['id']; ?>" class="btn edit-general-btn"><i class="fa-regular fa-pen-to-square"></i></a>
                        <a href="delete_restaurant_view_admin.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-cancel"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>