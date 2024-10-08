<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security_headers.php';

if (!isLoggedIn()) {
    redirectTo('login.php');
}

$conn = getDBConnection();

$restaurant_id = isset($_GET['restaurant_id']) ? intval($_GET['restaurant_id']) : 0;
$reservation_date = $reservation_time = $party_size = "";
$reservation_date_err = $reservation_time_err = $party_size_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    // Validar fecha de reserva
    list($date_valid, $date_result) = validateAndSanitize($_POST["reservation_date"], 'date');
    if (!$date_valid) {
        $reservation_date_err = $date_result;
    } else {
        $reservation_date = $date_result;
    }
    
    // Validar hora de reserva
    list($time_valid, $time_result) = validateAndSanitize($_POST["reservation_time"], 'time');
    if (!$time_valid) {
        $reservation_time_err = $time_result;
    } else {
        $reservation_time = $time_result;
    }
    
    // Validar tamaño del grupo
    list($size_valid, $size_result) = validateAndSanitize($_POST["party_size"], 'number');
    if (!$size_valid) {
        $party_size_err = $size_result;
    } else {
        $party_size = $size_result;
    }
    
    // Verificar errores de entrada antes de insertar en la base de datos
    if (empty($reservation_date_err) && empty($reservation_time_err) && empty($party_size_err)) {
        $sql = "INSERT INTO reservations (user_id, restaurant_id, reservation_date, reservation_time, party_size) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iissi", $param_user_id, $param_restaurant_id, $param_reservation_date, $param_reservation_time, $param_party_size);
            $param_user_id = $_SESSION["id"];
            $param_restaurant_id = $restaurant_id;
            $param_reservation_date = $reservation_date;
            $param_reservation_time = $reservation_time;
            $param_party_size = $party_size;
            
            if ($stmt->execute()) {
                redirectTo('index.php?reservation_success=1');
            } else {
                echo "Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
}

// Obtener información del restaurante
$restaurant_name = "";
if ($restaurant_id > 0) {
    $sql = "SELECT name FROM restaurants WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $restaurant_id);
        if ($stmt->execute()) {
            $stmt->bind_result($restaurant_name);
            $stmt->fetch();
        }
        $stmt->close();
    }
}

$conn->close();
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
                    <a class="nav-link" href="profile.php">Mi Perfil</a>
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="card border-0 card-general">
            <div class="card-body">
                <h3 class="title-general">Haz tu Reserva</h3>
                <?php
                if (!empty($restaurant_name)) {
                    echo "<h5 class='title-general'>Restaurante: $restaurant_name</h5>";
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?restaurant_id=" . $restaurant_id); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="reservation_date">Fecha de reserva:</label>
                        <input class="form-control" type="date" id="reservation_date" name="reservation_date" value="<?php echo $reservation_date; ?>">
                        <span class="text-corto-general"><?php echo $reservation_date_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reservation_time">Hora de reserva:</label>
                        <input class="form-control" type="time" id="reservation_time" name="reservation_time" value="<?php echo $reservation_time; ?>">
                        <span class="text-corto-general"><?php echo $reservation_time_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="party_size">Número de personas:</label>
                        <input class="form-control" type="number" id="party_size" name="party_size" min="1" value="<?php echo $party_size; ?>">
                        <span class="text-corto-general"><?php echo $party_size_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-general" type="submit" value="Reservar">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once './includes/plantillas/footer/footer.php'; ?>