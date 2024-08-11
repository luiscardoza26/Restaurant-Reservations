<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';
require_once 'includes/security_headers.php';


$search_query = $search_location = "";
$restaurants = [];

$sql = "SELECT r.*, 
               (SELECT AVG(rating) FROM reviews WHERE restaurant_id = r.id) as avg_rating 
        FROM restaurants r 
        WHERE name LIKE ? OR cuisine_type LIKE ?";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['query'])) {
    $search_query = trim($_GET['query']);
    $search_location = isset($_GET['location']) ? trim($_GET['location']) : "";

    $conn = getDBConnection();

    $sql = "SELECT r.*,
               (SELECT AVG(rating) FROM reviews WHERE restaurant_id = r.id) as avg_rating
        FROM restaurants r
        WHERE name LIKE ? OR cuisine_type LIKE ?";
    $params = ["%$search_query%", "%$search_query%"];
    $types = "ss";

    if (!empty($search_location)) {
        $sql .= " AND (address LIKE ? OR city LIKE ? OR state LIKE ?)";
        $params = array_merge($params, ["%$search_location%", "%$search_location%", "%$search_location%"]);
        $types .= "sss";
    }

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $restaurants[] = $row;
            }
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

