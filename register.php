<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security_headers.php';


$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = $nit_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    $conn = getDBConnection();

    $role = $_POST['role'];
    $nit = null;
    
    // Validar username
    list($username_valid, $username_result) = validateAndSanitize($_POST["username"], 'username');
    if (!$username_valid) {
        $username_err = $username_result;
    } else {
        $username = $username_result;
        // Verificar si el username ya existe en la base de datos
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "Este nombre de usuario ya está en uso.";
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    // Validar email
    list($email_valid, $email_result) = validateAndSanitize($_POST["email"], 'email');
    if (!$email_valid) {
        $email_err = $email_result;
    } else {
        $email = $email_result;
        // Verificar si el email ya existe en la base de datos
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = $email;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "Este correo electrónico ya está en uso.";
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    // Validar password
    list($password_valid, $password_result) = validateAndSanitize($_POST["password"], 'password');
    if (!$password_valid) {
        $password_err = $password_result;
    } else {
        $password = $password_result;
    }
    
    // Validar confirm_password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor confirme la contraseña.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }

    // Validar NIT basado en el rol
    if ($role === 'restaurant_admin') {
        $nit = trim($_POST['nit']);
        
        // Validación del NIT
        if (empty($nit)) {
            $nit_err = "Por favor ingrese el NIT.";
        } elseif (!preg_match('/^\d{9}$/', $nit)) {
            $nit_err = "El NIT debe contener exactamente 9 dígitos.";
        }
    } elseif ($role !== 'user') {
        $role_err = "Rol no válido.";
    }
    
    // Verificar errores de entrada antes de insertar en la base de datos
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($nit_err)) {
        $sql = "INSERT INTO users (username, email, password, role, nit) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $param_username, $param_email, $param_password, $param_role, $param_nit);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Crea un hash de la contraseña
            $param_role = $role;
            $param_nit = $nit;
            
            if ($stmt->execute()) {
                redirectTo('login.php');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

<?php include_once './includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index.php">Inicio</a>
                    <a class="nav-link" href="login.php">Iniciar sesión</a>
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
<?php include_once './includes/plantillas/footer/footer.php'; ?>