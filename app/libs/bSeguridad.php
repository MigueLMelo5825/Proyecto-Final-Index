<?php

// -------------------------------------------------------------
// FUNCIONES DE HASH
// -------------------------------------------------------------
function encriptar($password, $cost = 10) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
}

function comprobarhash($pass, $passBD) {
    return password_verify($pass, $passBD);
}

// -------------------------------------------------------------
// SESSION MANAGER
// -------------------------------------------------------------
class SessionManager {

    private $loginPage;
    private $timeout;

    public function __construct($loginPage = 'index.php?ctl=login', $timeout = 600) {
        $this->loginPage = $loginPage;
        $this->timeout = $timeout;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ---------------------------------------------------------
    // SETTER Y GETTER
    // ---------------------------------------------------------
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        return $_SESSION[$key] ?? null;
    }

    // ---------------------------------------------------------
    // COMPROBAR NIVEL DE ACCESO
    // ---------------------------------------------------------
    public function hasLevel($requiredLevel) {
        $nivel = $_SESSION['nivel'] ?? 0;
        return $nivel >= $requiredLevel;
    }

    // ---------------------------------------------------------
    // SEGURIDAD GENERAL
    // ---------------------------------------------------------
    public function checkSecurity() {

        // Si no hay usuario logueado → fuera
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: " . $this->loginPage);
            exit;
        }

        // Timeout de inactividad
        if (isset($_SESSION['LAST_ACTIVITY']) &&
            (time() - $_SESSION['LAST_ACTIVITY'] > $this->timeout)) {

            session_unset();
            session_destroy();
            header("Location: " . $this->loginPage);
            exit;
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    // ---------------------------------------------------------
    // LOGOUT
    // ---------------------------------------------------------
    public function logout() {
        session_unset();
        session_destroy();
    }
}
?>
