<?php
session_start();

define('BASE_PATH', dirname(__DIR__));

// Detecta automáticamente el subdirectorio donde está instalado el proyecto
// Ej: /gestion-usuario/public/index.php → BASE_URL = /gestion-usuario/public/index.php
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/index.php');
define('ASSETS_URL', rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\'));

spl_autoload_register(function ($class) {
    $dirs = [BASE_PATH . '/models/', BASE_PATH . '/controllers/', BASE_PATH . '/middleware/'];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});

require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/models/Log.php';
require_once BASE_PATH . '/models/Auth.php';
require_once BASE_PATH . '/models/Usuario.php';
require_once BASE_PATH . '/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/UsuarioController.php';

$action = $_GET['action'] ?? 'home';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$method = $_SERVER['REQUEST_METHOD'];

$authCtrl    = new AuthController();
$usuarioCtrl = new UsuarioController();

match (true) {
    $action === 'home'           => (function() use ($authCtrl) {
        if ((new Auth())->isLoggedIn()) {
            header('Location: ' . BASE_URL . '?action=perfil'); exit;
        }
        header('Location: ' . BASE_URL . '?action=login'); exit;
    })(),
    $action === 'login'  && $method === 'GET'  => $authCtrl->showLogin(),
    $action === 'login'  && $method === 'POST' => $authCtrl->login(),
    $action === 'logout'                        => $authCtrl->logout(),
    $action === 'register' && $method === 'GET'  => $authCtrl->showRegister(),
    $action === 'register' && $method === 'POST' => $authCtrl->register(),
    $action === 'forgotPassword' && $method === 'GET'  => $authCtrl->showForgotPassword(),
    $action === 'forgotPassword' && $method === 'POST' => $authCtrl->forgotPassword(),
    $action === 'resetPassword'  && $method === 'GET'  => $authCtrl->showResetPassword(),
    $action === 'resetPassword'  && $method === 'POST' => $authCtrl->resetPassword(),
    $action === 'usuarios'                      => $usuarioCtrl->index(),
    $action === 'perfil'                        => $usuarioCtrl->perfil(),
    $action === 'edit'   && $id !== null        => $usuarioCtrl->edit($id),
    $action === 'update' && $id !== null && $method === 'POST' => $usuarioCtrl->update($id),
    $action === 'delete' && $id !== null        => $usuarioCtrl->delete($id),
    $action === 'changePassword' && $method === 'POST' => $usuarioCtrl->changePassword(),
    $action === 'api/usuarios'                  => $usuarioCtrl->apiUsuarios(),
    $action === 'logs'                          => $usuarioCtrl->logs(),
    default => (function() {
        http_response_code(404);
        echo '<h1>404 - Página no encontrada</h1>';
    })(),
};
