<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';
require_once '../includes/security_headers.php';

if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('../login.php');
}

$conn = getDBConnection();

$id = $name = $address = $phone = "";
$name_err = $address_err = $phone_err = "";
$cuisine_type = "";
$cuisine_type_err = "";

// Procesar datos del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    // Validar ID
    if (empty(trim($_POST["id"]))) {
        redirectTo('restaurants.php');
    } else {
        $id = trim($_POST["id"]);
    }
    
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
    
    // Verificar errores de entrada antes de actualizar en la base de datos
    if (empty($name_err) && empty($address_err) && empty($phone_err) && empty($cuisine_type_err)) {
        $sql = "UPDATE restaurants SET name = ?, address = ?, phone = ?, cuisine_type = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $param_name, $param_address, $param_phone, $param_cuisine_type, $param_id);
            $param_name = $name;
            $param_address = $address;
            $param_phone = $phone;
            $param_cuisine_type = $cuisine_type;
            $param_id = $id;
            
            if ($stmt->execute()) {
                redirectTo('restaurants.php');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
    
} else {
    // Verificar si se proporcionó un ID válido
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        
        $sql = "SELECT * FROM restaurants WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $param_id);
            $param_id = $id;
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    
                    $name = $row["name"];
                    $address = $row["address"];
                    $phone = $row["phone"];
                    $cuisine_type = $row["cuisine_type"];
                } else {
                    redirectTo('restaurants.php');
                    exit();
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
        }
        $stmt->close();
    } else {
        redirectTo('restaurants.php');
        exit();
    }
}

$conn->close();
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
        <div class="card border-0 card-general ">
            <div class="card-body">
                <h2 class="title-general">Editar Restaurante</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input class="form-control" type="hidden" name="id" value="<?php echo $id; ?>">
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
                        <input class="form-control" class="form-control" type="text" id="cuisine_type" name="cuisine_type" value="<?php echo $cuisine_type; ?>">
                        <span class="text-corto-general"><?php echo $cuisine_type_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-general" type="submit" value="Actualizar Restaurante">
                        <a href="restaurants.php" class="btn btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>
