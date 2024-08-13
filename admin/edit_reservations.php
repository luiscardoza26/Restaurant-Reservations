<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';
require_once '../includes/security_headers.php';

// Verificar si el usuario ha iniciado sesión y es un administrador
if (!isAdmin() && !isRestaurantAdmin()) {
    redirectTo('../login.php');
}

$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$conn = getDBConnection();

// Obtener la información de la reserva
$sql = "SELECT r.*, u.username, res.name as restaurant_name
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN restaurants res ON r.restaurant_id = res.id
        WHERE r.id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $reservation_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($reservation = $result->fetch_assoc()) {
            // La reserva existe
        } else {
            // La reserva no existe
            $_SESSION['error_message'] = "La reserva no existe.";
            redirectTo('reservations.php');
        }
    } else {
        echo "Oops! Algo salió mal al obtener la reserva. Por favor, inténtelo de nuevo más tarde.";
    }
    $stmt->close();
} else {
    echo "Oops! Algo salió mal al preparar la consulta. Por favor, inténtelo de nuevo más tarde.";
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("Error de validación CSRF.");
    }

    $fields_to_update = [];
    $types = "";
    $params = [];

    // Validar y preparar los campos que se van a actualizar
    if (!empty($_POST["reservation_date"]) && $_POST["reservation_date"] != $reservation['reservation_date']) {
        list($date_valid, $date_result) = validateAndSanitize($_POST["reservation_date"], 'date');
        if ($date_valid) {
            $fields_to_update[] = "reservation_date = ?";
            $types .= "s";
            $params[] = $date_result;
        }
    }

    if (!empty($_POST["reservation_time"]) && $_POST["reservation_time"] != $reservation['reservation_time']) {
        list($time_valid, $time_result) = validateAndSanitize($_POST["reservation_time"], 'time');
        if ($time_valid) {
            $fields_to_update[] = "reservation_time = ?";
            $types .= "s";
            $params[] = $time_result;
        }
    }

    if (!empty($_POST["party_size"]) && $_POST["party_size"] != $reservation['party_size']) {
        list($size_valid, $size_result) = validateAndSanitize($_POST["party_size"], 'number');
        if ($size_valid) {
            $fields_to_update[] = "party_size = ?";
            $types .= "i";
            $params[] = $size_result;
        }
    }

    // Manejar el campo status
    if (isset($_POST["status"]) && $_POST["status"] !== $reservation['status']) {
        $status_result = trim($_POST["status"]);
        // Validar que el status sea uno de los valores permitidos
        $allowed_statuses = ['pending', 'confirmed', 'cancelled'];
        if (in_array($status_result, $allowed_statuses)) {
            $fields_to_update[] = "status = ?";
            $types .= "s";
            $params[] = $status_result;
        } else {
            $_SESSION['error_message'] = "Estado de reserva no válido.";
        }
    }

    // Si hay campos para actualizar, proceder con la actualización
    if (!empty($fields_to_update)) {
        $sql = "UPDATE reservations SET " . implode(", ", $fields_to_update) . " WHERE id = ?";
        $types .= "i";
        $params[] = $reservation_id;

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param($types, ...$params);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "La reserva ha sido actualizada exitosamente.";
                redirectTo('reservations.php');
            } else {
                echo "Oops! Algo salió mal al actualizar la reserva. Error: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Oops! Algo salió mal al preparar la consulta de actualización. Error: " . $conn->error;
        }
    } else {
        $_SESSION['info_message'] = "No se realizaron cambios en la reserva.";
        redirectTo('reservations.php');
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
        <div class="card border-0 card-general">
            <div class="card-body">
                <h2 class="title-general">Editar Reserva</h2>
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo "<p class='text-corto-general'>" . $_SESSION['error_message'] . "</p>";
                    unset($_SESSION['error_message']);
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $reservation_id); ?>" method="post">
                    <input class="form-control" type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="username">Usuario:</label>
                        <input class="form-control" type="text" id="username" name="username" value="<?php echo e($reservation['username']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="restaurant_name">Restaurante:</label>
                        <input class="form-control" type="text" id="restaurant_name" name="restaurant_name" value="<?php echo e($reservation['restaurant_name']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reservation_date">Fecha de reserva:</label>
                        <input class="form-control" type="date" id="reservation_date" name="reservation_date" value="<?php echo $reservation['reservation_date']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reservation_time">Hora de reserva:</label>
                        <input class="form-control" type="time" id="reservation_time" name="reservation_time" value="<?php echo $reservation['reservation_time']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="party_size">Número de personas:</label>
                        <input class="form-control" type="number" id="party_size" name="party_size" value="<?php echo $reservation['party_size']; ?>" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="status">Status:</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending" <?php echo ($reservation['status'] == 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="confirmed" <?php echo ($reservation['status'] == 'confirmed') ? 'selected' : ''; ?>>Aprobada</option>
                            <option value="cancelled" <?php echo ($reservation['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="submit" value="Actualizar Reserva" class="btn btn-general">
                        <a href="reservations.php" class="btn btn-cancel">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include_once '../includes/plantillas/footer/footer.php'; ?>
