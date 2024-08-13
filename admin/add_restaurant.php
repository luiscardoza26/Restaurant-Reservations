<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';
require_once '../includes/security_headers.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('../login.php');
}

$name = $address = $phone = "";
$name_err = $address_err = $phone_err = "";
$cuisine_type = "";
$cuisine_type_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);
    
    $conn = getDBConnection();
    
    // Validar nombre
    list($name_valid, $name_result) = validateAndSanitize($_POST["name"], 'name');
    if (!$name_valid) {
        $name_err = $name_result;
    } else {
        $name = $name_result;
    }
    
    // Validar dirección
    list($address_valid, $address_result) = validateAndSanitize($_POST["address"], 'name');
    if (!$address_valid) {
        $address_err = $address_result;
    } else {
        $address = $address_result;
    }
    
    // Validar teléfono
    list($phone_valid, $phone_result) = validateAndSanitize($_POST["phone"], 'phone');
    if (!$phone_valid) {
        $phone_err = $phone_result;
    } else {
        $phone = $phone_result;
    }

    // Validar tipo de cocina
    list($cuisine_type_valid, $cuisine_type_result) = validateAndSanitize($_POST["cuisine_type"], 'name');
    if (!$cuisine_type_valid) {
        $cuisine_type_err = $cuisine_type_result;
    } else {
        $cuisine_type = $cuisine_type_result;
    }
    
    // Verificar errores de entrada antes de insertar en la base de datos
    if (empty($name_err) && empty($address_err) && empty($phone_err)) {
        $sql = "INSERT INTO restaurants (name, address, phone, cuisine_type) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $param_name, $param_address, $param_phone, $param_cusine_type);
            $param_name = $name;
            $param_address = $address;
            $param_phone = $phone;
            $param_cusine_type = $cuisine_type;
            
            if ($stmt->execute()) {
                redirectTo('restaurants.php');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>


<?php include_once '../includes/plantillas/header/header.php'; ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <h5 class="title">Sistema de Reservas de Restaurantes</h5>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index.php">Inicio</a>
                    <a class="nav-link" href="restaurants.php">Panel de Administrador</a>
                    <a class="nav-link" href="../profile.php">Mi Perfil</a>
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