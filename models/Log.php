<?php
require_once __DIR__ . '/../config/Database.php';

class Log {
    public static function register(?int $usuarioId, string $accion, string $descripcion = ''): void {
        try {
            $db = Database::getInstance()->getConnection();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $stmt = $db->prepare("INSERT INTO logs (usuario_id, accion, descripcion, ip) VALUES (:uid, :accion, :desc, :ip)");
            $stmt->execute([':uid' => $usuarioId, ':accion' => $accion, ':desc' => $descripcion, ':ip' => $ip]);
        } catch (Exception $e) {}
    }

    public static function getAll(int $page = 1, int $perPage = 20): array {
        $db = Database::getInstance()->getConnection();
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("SELECT l.*, u.nombre, u.email FROM logs l LEFT JOIN usuarios u ON l.usuario_id = u.id ORDER BY l.created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countAll(): int {
        $db = Database::getInstance()->getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM logs")->fetchColumn();
    }
}
