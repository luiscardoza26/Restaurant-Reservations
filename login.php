<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';

$username = $password = "";
$username_err = $password_err = $login_err = "";

$max_attempts = 5;
$lockout_time = 15 * 60; // 15 minutos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateCSRFToken($_POST['csrf_token']);

    $conn = getDBConnection();
    
    // Validar username
    list($username_valid, $username_result) = validateAndSanitize($_POST["username"], 'username');
    if (!$username_valid) {
        $username_err = $username_result;
    } else {
        $username = $username_result;
    }
    
    // Validar password
    list($password_valid, $password_result) = validateAndSanitize($_POST["password"], 'password');
    if (!$password_valid) {
        $password_err = $password_result;
    } else {
        $password = $password_result;
    }
    
    // Validar credenciales
    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password, $role);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;
                            
                            if (isAdmin() || isRestaurantAdmin()){
                                redirectTo('index_view_admin.php');
                            }else {
                                redirectTo('index_view.php');
                            }
                        } else {
                            $login_err = "Usuario o contraseña incorrectos.";
                        }
                    }
                } else {
                    $login_err = "Usuario o contraseña incorrectos.";
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            $stmt->close();
        }
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password, role, login_attempts, last_login_attempt FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password, $role, $login_attempts, $last_login_attempt);
                    if ($stmt->fetch()) {
                        // Verificar si la cuenta está bloqueada
                        if ($login_attempts >= $max_attempts && (time() - strtotime($last_login_attempt)) < $lockout_time) {
                            $login_err = "La cuenta está bloqueada. Por favor, inténtelo de nuevo más tarde.";
                        } else {
                            if (password_verify($password, $hashed_password)) {
                                // Reiniciar intentos de inicio de sesión
                                $update_sql = "UPDATE users SET login_attempts = 0 WHERE id = ?";
                                $update_stmt = $conn->prepare($update_sql);
                                $update_stmt->bind_param("i", $id);
                                $update_stmt->execute();
                                $update_stmt->close();

                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["role"] = $role;
                                
                                redirectTo('index_view.php');
                            } else {
                                // Incrementar intentos de inicio de sesión
                                $update_sql = "UPDATE users SET login_attempts = login_attempts + 1, last_login_attempt = CURRENT_TIMESTAMP WHERE id = ?";
                                $update_stmt = $conn->prepare($update_sql);
                                $update_stmt->bind_param("i", $id);
                                $update_stmt->execute();
                                $update_stmt->close();

                                $login_err = "Usuario o contraseña incorrectos.";
                            }
                        }
                    }
                } else {
                    $login_err = "Usuario o contraseña incorrectos.";
                }
            } else {
                echo "Oops! Algo salió mal. Por favor, inténtelo de nuevo más tarde.";
            }

            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

