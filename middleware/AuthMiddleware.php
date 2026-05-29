<?php
class AuthMiddleware {
    public static function check(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    public static function adminOnly(): void {
        self::check();
        if ($_SESSION['user']['rol'] !== 'admin') {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }
}
