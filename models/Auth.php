<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/Log.php';

class Auth {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login(string $email, string $password): bool {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email AND activo = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id'     => $user['id'],
                'nombre' => $user['nombre'],
                'email'  => $user['email'],
                'rol'    => $user['rol'],
                'avatar' => $user['avatar'],
            ];
            $u = new Usuario();
            $u->updateLastAccess($user['id']);
            Log::register($user['id'], 'login', 'Inicio de sesión exitoso');
            return true;
        }

        Log::register(null, 'login_fallido', "Intento fallido con email: $email");
        return false;
    }

    public function logout(): void {
        if (isset($_SESSION['user'])) {
            Log::register($_SESSION['user']['id'], 'logout', 'Cierre de sesión');
        }
        session_destroy();
        header('Location: ' . BASE_URL . '?action=login');
        exit;
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user']);
    }

    public function getCurrentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public function hasRole(string $role): bool {
        return isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === $role;
    }
}
