<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/Mailer.php';

class AuthController {
    private Auth $auth;
    private Usuario $usuario;

    public function __construct() {
        $this->auth    = new Auth();
        $this->usuario = new Usuario();
    }

    public function showLogin(): void {
        if ($this->auth->isLoggedIn()) {
            header('Location: ' . BASE_URL . '?action=perfil');
            exit;
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void {
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if ($this->auth->login($email, $password)) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Credenciales incorrectas o cuenta inactiva.'];
        header('Location: ' . BASE_URL . '?action=login');
        exit;
    }

    public function logout(): void {
        $this->auth->logout();
    }

    public function showRegister(): void {
        if ($this->auth->isLoggedIn()) {
            header('Location: ' . BASE_URL);
            exit;
        }
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register(): void {
        $nombre   = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        if ($password !== $confirm) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Las contraseñas no coinciden.'];
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'La contraseña debe tener al menos 6 caracteres.'];
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        if ($this->usuario->emailExists($email)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'El email ya está registrado.'];
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        $this->usuario->setNombre($nombre);
        $this->usuario->setEmail($email);
        $this->usuario->setPassword($password);
        $this->usuario->setRol('user');

        $errors = $this->usuario->validate();
        if (!empty($errors)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => implode(' ', $errors)];
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        $this->usuario->create();
        Log::register(null, 'registro', "Nuevo usuario registrado: $email");
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Cuenta creada. Ya puedes iniciar sesión.'];
        header('Location: ' . BASE_URL . '?action=login');
        exit;
    }

    public function showForgotPassword(): void {
        require __DIR__ . '/../views/auth/forgot_password.php';
    }

    public function forgotPassword(): void {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $user  = $this->usuario->getByEmail($email);

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->usuario->setResetToken($email, $token, $expires);

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $base   = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
            $link   = "{$scheme}://{$_SERVER['HTTP_HOST']}{$base}/public/index.php?action=resetPassword&token=$token";

            $sent = Mailer::send($email, $user['nombre'], 'Restablecer contraseña', Mailer::buildResetEmail($link));
            Log::register($user['id'], 'forgot_password', 'Solicitud de recuperación de contraseña');

            if (!$sent) {
                // Fallback para desarrollo: mostrar el link directamente en pantalla
                $_SESSION['reset_link'] = $link;
            }
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Si el email existe, recibirás un enlace de recuperación.'];
        header('Location: ' . BASE_URL . '?action=login');
        exit;
    }

    public function showResetPassword(): void {
        $token = $_GET['token'] ?? '';
        $user  = $this->usuario->getByResetToken($token);
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Token inválido o expirado.'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
        require __DIR__ . '/../views/auth/reset_password.php';
    }

    public function resetPassword(): void {
        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        $user     = $this->usuario->getByResetToken($token);

        if (!$user || $password !== $confirm || strlen($password) < 6) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Error al restablecer la contraseña.'];
            header("Location: " . BASE_URL . "?action=resetPassword&token=$token");
            exit;
        }

        $u = new Usuario();
        $u->setId($user['id']);
        $u->updatePassword($password);
        $this->usuario->clearResetToken($user['id']);
        Log::register($user['id'], 'reset_password', 'Contraseña restablecida');
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Contraseña actualizada correctamente.'];
        header('Location: ' . BASE_URL . '?action=login');
        exit;
    }
}
