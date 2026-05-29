<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class UsuarioController {
    private Usuario $usuario;
    private Auth $auth;

    public function __construct() {
        $this->usuario = new Usuario();
        $this->auth    = new Auth();
    }

    public function index(): void {
        AuthMiddleware::adminOnly();
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $search    = htmlspecialchars(trim($_GET['search'] ?? ''));
        $rolFilter = $_GET['rol'] ?? '';
        $perPage   = 10;
        $users     = $this->usuario->getAll($page, $perPage, $search, $rolFilter);
        $total     = $this->usuario->countAll($search, $rolFilter);
        $pages     = (int)ceil($total / $perPage);
        require __DIR__ . '/../views/usuarios/index.php';
    }

    public function perfil(): void {
        AuthMiddleware::check();
        $currentUser = $this->auth->getCurrentUser();
        $user        = $this->usuario->getById($currentUser['id']);
        require __DIR__ . '/../views/usuarios/perfil.php';
    }

    public function edit(int $id): void {
        AuthMiddleware::check();
        $currentUser = $this->auth->getCurrentUser();
        if ($currentUser['rol'] !== 'admin' && $currentUser['id'] !== $id) {
            AuthMiddleware::adminOnly();
        }
        $user = $this->usuario->getById($id);
        if (!$user) { $this->notFound(); return; }
        require __DIR__ . '/../views/usuarios/edit.php';
    }

    public function update(int $id): void {
        AuthMiddleware::check();
        $currentUser = $this->auth->getCurrentUser();
        if ($currentUser['rol'] !== 'admin' && $currentUser['id'] !== $id) {
            AuthMiddleware::adminOnly();
        }

        $existing = $this->usuario->getById($id);
        if (!$existing) { $this->notFound(); return; }

        $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $email  = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $rol    = ($currentUser['rol'] === 'admin') ? ($_POST['rol'] ?? 'user') : $existing['rol'];
        $activo = ($currentUser['rol'] === 'admin') ? (bool)($_POST['activo'] ?? false) : (bool)$existing['activo'];

        if ($this->usuario->emailExists($email, $id)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'El email ya está en uso.'];
            header("Location: " . BASE_URL . "?action=edit&id=$id");
            exit;
        }

        $avatarPath = $existing['avatar'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarPath = $this->handleAvatarUpload($id);
            if ($avatarPath === false) {
                $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error al subir el avatar. Máximo 2MB, formato JPG/PNG/GIF/WEBP.'];
                header("Location: " . BASE_URL . "?action=edit&id=$id");
                exit;
            }
        }

        $this->usuario->setId($id);
        $this->usuario->setNombre($nombre);
        $this->usuario->setEmail($email);
        $this->usuario->setRol($rol);
        $this->usuario->setActivo($activo);
        $this->usuario->setAvatar($avatarPath);

        $errors = $this->usuario->validate();
        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => implode(' ', $errors)];
            header("Location: " . BASE_URL . "?action=edit&id=$id");
            exit;
        }

        $this->usuario->update();

        if ($currentUser['id'] === $id) {
            $_SESSION['user']['nombre'] = $nombre;
            $_SESSION['user']['email']  = $email;
            $_SESSION['user']['avatar'] = $avatarPath;
        }

        Log::register($currentUser['id'], 'update_usuario', "Usuario ID $id actualizado");
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Usuario actualizado correctamente.'];
        $redirect = ($currentUser['rol'] === 'admin') ? BASE_URL . '?action=usuarios' : BASE_URL . '?action=perfil';
        header("Location: $redirect");
        exit;
    }

    public function delete(int $id): void {
        AuthMiddleware::adminOnly();
        $currentUser = $this->auth->getCurrentUser();
        if ($currentUser['id'] === $id) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'No puedes eliminar tu propia cuenta.'];
            header('Location: ' . BASE_URL . '?action=usuarios');
            exit;
        }
        $this->usuario->delete($id);
        Log::register($currentUser['id'], 'delete_usuario', "Usuario ID $id eliminado");
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Usuario eliminado.'];
        header('Location: ' . BASE_URL . '?action=usuarios');
        exit;
    }

    public function changePassword(): void {
        AuthMiddleware::check();
        $currentUser = $this->auth->getCurrentUser();
        $id          = $currentUser['id'];
        $current     = $_POST['current_password'] ?? '';
        $new         = $_POST['new_password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';

        $userData = $this->usuario->getByEmail($currentUser['email']);
        if (!$userData || !password_verify($current, $userData['password'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'La contraseña actual es incorrecta.'];
            header('Location: ' . BASE_URL . '?action=perfil');
            exit;
        }

        if ($new !== $confirm || strlen($new) < 6) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Las contraseñas no coinciden o son muy cortas (mín. 6 caracteres).'];
            header('Location: ' . BASE_URL . '?action=perfil');
            exit;
        }

        $u = new Usuario();
        $u->setId($id);
        $u->updatePassword($new);
        Log::register($id, 'change_password', 'Cambio de contraseña');
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Contraseña cambiada correctamente.'];
        header('Location: ' . BASE_URL . '?action=perfil');
        exit;
    }

    public function apiUsuarios(): void {
        $token = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
        if ($token !== 'mi-token-secreto-api') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $users = $this->usuario->getAll(1, 100);
        header('Content-Type: application/json');
        echo json_encode(['data' => $users]);
        exit;
    }

    public function logs(): void {
        AuthMiddleware::adminOnly();
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $logs  = Log::getAll($page, 20);
        $total = Log::countAll();
        $pages = (int)ceil($total / 20);
        require __DIR__ . '/../views/admin/logs.php';
    }

    private function handleAvatarUpload(int $userId): string|false {
        $file    = $_FILES['avatar'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024;

        if (!in_array($file['type'], $allowed) || $file['size'] > $maxSize) return false;

        $uploadDir = BASE_PATH . '/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = "avatar_{$userId}_" . time() . ".$ext";
        $dest     = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
        return "/uploads/avatars/$filename";
    }

    private function notFound(): void {
        http_response_code(404);
        echo '<h1>Usuario no encontrado</h1>';
    }
}
