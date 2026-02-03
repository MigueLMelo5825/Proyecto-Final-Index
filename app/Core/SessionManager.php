<?php
/**
 * ================================================================
 *  SessionManager
 *  ---------------------------------------------------------------
 *  Gestión segura de sesiones con:
 *    - Cookie hardening
 *    - Fingerprint (IP + User-Agent)
 *    - Timeout por inactividad
 *    - RBAC numérico (Guest/User)
 *    - Regeneración antifijación
 *
 *  @author    Heike Bonilla
 */

class SessionManager
{
    // -------------------------------------------------------------
    // CONFIGURACIÓN
    // -------------------------------------------------------------
     /** * Página a la que se redirige tras logout o timeout.
      * @var string */
    private string $loginPage;
    /** * Tiempo máximo de inactividad permitido (segundos). 
     * @var int */
    private int $timeout;
    /**
     * Nivel mínimo para invitados.
     * @var int
     */
    private const ROLE_GUEST = 1;
    /** * Nivel mínimo para usuarios autenticados. 
     *  @var int */
    private const ROLE_USER  = 2;
    /** * Constructor del gestor de sesiones. * 
     * @param string $loginPage Página de destino tras logout o timeout. 
     * @param int $timeout Tiempo máximo de inactividad en segundos. */
    public function __construct(
        string $loginPage = 'index.php',
        int $timeout = 600
    ) {
        $this->loginPage = $loginPage;
        $this->timeout   = $timeout;

        $this->start();
    }

    // -------------------------------------------------------------
    // APERTURA SEGURA DE SESIÓN
    // -------------------------------------------------------------
    /** * Inicia o reanuda la sesión aplicando configuraciones de seguridad. 
     * Debe llamarse al inicio de cada script. 
     * @param array $options Opciones de configuración: 
     * -httponly (bool) 
     * -samesite (string) 
     * secure (bool) 
     * @return void */
    public function start(array $options = []): void
    {
        $config = array_merge([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => false
        ], $options);

        ini_set('session.cookie_httponly', $config['httponly'] ? '1' : '0');
        ini_set('session.cookie_samesite', $config['samesite']);
        ini_set('session.cookie_secure',   $config['secure'] ? '1' : '0');
        ini_set('session.use_strict_mode', '1');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();

            if (!isset($_SESSION['usuarioNivel'])) {
                $_SESSION['usuarioNivel'] = self::ROLE_GUEST;
            }
        }
    }

    // -------------------------------------------------------------
    // LOGIN Y LOGOUT
    // -------------------------------------------------------------
     /** * Registra los datos del usuario en la sesión tras un login exitoso. 
      * Aplica regeneración antifijación y almacena fingerprint. 
     * @param mixed $id ID único del usuario. 
     * @param string $name Nombre del usuario. 
     * * @param int $level Nivel de acceso (RBAC). 
     * * * @return void */
    public function login($id, string $name, int $level = self::ROLE_USER): void
    {
        session_regenerate_id(true);

        $_SESSION['usuarioId']     = $id;
        $_SESSION['usuarioNombre'] = $name;
        $_SESSION['usuarioNivel']  = $level;

        $_SESSION['remoteAddr'] = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $_SESSION['userAgent']  = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Agent';

        $this->refreshActivity();
    }

    /** * Realiza el logout canónico: 
     *  1. Limpia memoria 
     *  2. Destruye sesión en servidor 
     *  3. Elimina cookie en cliente 
     *  4. Redirige a la página configurada
     * @return void */
    public function logout(): void
    {
        session_unset();
        session_destroy();

        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );

        header("Location: {$this->loginPage}");
        exit;
    }

    // -------------------------------------------------------------
    // SEGURIDAD: FINGERPRINT + TIMEOUT
    // -------------------------------------------------------------
     /** * Ejecuta todas las comprobaciones de seguridad: 
      * * - Fingerprint (IP + User-Agent) 
     * - Timeout por inactividad 
     * * Debe llamarse al inicio de cada script protegido. 
     * 
     * @return void */
    public function checkSecurity(): void
    {
        if (!$this->isLoggedIn()) {
            return;
        }

        // Fingerprint
        $storedAddr  = $_SESSION['remoteAddr'] ?? '';
        $storedAgent = $_SESSION['userAgent'] ?? '';

        $currentAddr  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $currentAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Agent';

        if ($storedAddr !== $currentAddr || $storedAgent !== $currentAgent) {
            $this->logout();
        }

        // Timeout
        if (isset($_SESSION['lastActivity']) &&
            (time() - $_SESSION['lastActivity'] > $this->timeout)) {
            $this->logout();
        }

        $this->refreshActivity();
    }
    /** * Actualiza la marca de tiempo de la última actividad. 
     * @return void */
    private function refreshActivity(): void
    {
        $_SESSION['lastActivity'] = time();
    }

    // -------------------------------------------------------------
    // GETTERS
    // -------------------------------------------------------------
    /** * Obtiene el ID del usuario actual. 
     * @return mixed|null ID del usuario o null si no está logueado. */
    public function getUserId()
    {
        return $_SESSION['usuarioId'] ?? null;
    }
    /** * Obtiene el nombre del usuario actual. 
     * @return string Nombre del usuario o cadena vacía si no está logueado. */
    public function getUserName(): string
    {
        return $_SESSION['usuarioNombre'] ?? '';
    }
    /** * Obtiene el nivel de acceso del usuario actual. 
     * @return int Nivel de acceso (RBAC). */
    public function getUserLevel(): int
    {
        return $_SESSION['usuarioNivel'] ?? self::ROLE_GUEST;
    }
    /** * Obtiene un valor de la sesión por índice. 
     * @param string $index Índice de la sesión a obtener. 
     * @return mixed|null Valor de la sesión o null si no existe. */
    public function get(string $index)
    {
        return $_SESSION[$index] ?? null;       
    }

    // -------------------------------------------------------------
    // ESTADO Y RBAC
    // -------------------------------------------------------------
    /** * Comprueba si el usuario está logueado. 
     * @return bool True si el usuario está autenticado, false si es invitado. */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['usuarioId']) &&
               $this->getUserLevel() > self::ROLE_GUEST;
    }
    /** * Comprueba si el usuario tiene el nivel de acceso requerido. 
     * @param int $requiredLevel Nivel mínimo requerido. 
     * @return bool True si el usuario tiene el nivel requerido, false en caso contrario. */
    public function hasLevel(int $requiredLevel): bool
    {
       
        return $this->getUserLevel() >= $requiredLevel;
    }
}
?>