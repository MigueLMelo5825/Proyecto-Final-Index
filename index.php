<!-- <?php
// -------------------------------------------------------------
// Front Controller del mini-framework
// -------------------------------------------------------------

require_once __DIR__ . '/../app/libs/Config.php';
require_once __DIR__ . '/../app/libs/bGeneral.php';
require_once __DIR__ . '/../app/libs/bSeguridad.php';
require_once __DIR__ . '/../app/core/autoload.php';


// -------------------------------------------------------------
// Sesión segura
// -------------------------------------------------------------
$session = new SessionManager(
    loginPage: 'index.php?ctl=inicio',
    timeout: 600
);

// Comprobaciones de seguridad (fingerprint + timeout)
$session->checkSecurity();

// -------------------------------------------------------------
// Mapa de rutas
// -------------------------------------------------------------
$map = [
  
    'inicio'            => ['controller' => 'InicioController',        'action' => 'inicio',          'nivel' => 1]
    
];

// -------------------------------------------------------------
// Resolución de ruta
// -------------------------------------------------------------
$ruta = $_GET['ctl'] ?? 'inicio';

if (!isset($map[$ruta])) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404: Ruta '$ruta' no encontrada</h1>";
    exit;
}

$controllerName = $map[$ruta]['controller'];
$actionName     = $map[$ruta]['action'];
$requiredLevel  = $map[$ruta]['nivel'];

// -------------------------------------------------------------
// Comprobación de permisos
// -------------------------------------------------------------
if (!$session->hasLevel($requiredLevel)) {
    header("HTTP/1.0 403 Forbidden");
    echo "<h1>403: No tienes permisos para acceder a esta acción</h1>";
    exit;
}

// -------------------------------------------------------------
// Ejecución del controlador
// -------------------------------------------------------------
$controller = new $controllerName($session);

if (!method_exists($controller, $actionName)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404: Acción '$actionName' no encontrada en $controllerName</h1>";
    exit;
}

$controller->$actionName();
?> -->