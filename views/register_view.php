<?php require_once '../register.php'; ?>
<?php require_once '../includes/security_headers.php'; ?>
<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="index_view.php">Inicio</a>
                    <a class="nav-link" href="login_view.php">Iniciar sesión</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="card border-0 card-general">
            <div class="card-body">
                <h2 class="title-general">Registro</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="username">Nombre de usuario:</label>
                        <input class="form-control" type="text" id="username" name="username" value="<?php echo $username; ?>">
                        <span class="text-corto-general"><?php echo $username_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Correo electrónico:</label>
                        <input class="form-control" type="email" id="email" name="email" value="<?php echo $email; ?>">
                        <span class="text-corto-general"><?php echo $email_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña:</label>
                        <input class="form-control" type="password" id="password" name="password">
                        <span class="text-corto-general"><?php echo $password_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="confirm_password">Confirmar contraseña:</label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password">
                        <span class="text-corto-general"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="role">Rol:</label>
                        <select class="form-control" id="role" name="role">
                            <option value="user">Usuario</option>
                            <option value="restaurant_admin">Administrador de Restaurante</option>
                        </select>
                    </div>
                    <div class="mb-3" id="nit-container">
                        <label class="form-label" for="nit">NIT:</label>
                        <input class="form-control" type="text" id="nit" name="nit">
                        <span class="text-corto-general"><?php echo $nit_err; ?></span>
                    </div>
                    <div>
                        <input class="btn btn-general" type="submit" value="Registrarse">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script >
        document.addEventListener('DOMContentLoaded', function() {
            let roleSelect = document.getElementById('role');
            let nitContainer = document.getElementById('nit-container');

            function toggleNitField() {
                if (roleSelect.value === 'restaurant_admin') {
                    nitContainer.style.display = 'block';
                } else {
                    nitContainer.style.display = 'none';
                }
            }

            roleSelect.addEventListener('change', toggleNitField);
            
            // Ejecuta la función inmediatamente para manejar el estado inicial
            toggleNitField();
        });
    </script>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>