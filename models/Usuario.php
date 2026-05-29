<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario {
    private PDO $db;
    private int $id;
    private string $nombre;
    private string $email;
    private string $password;
    private string $rol;
    private bool $activo;
    private ?string $avatar;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getEmail(): string { return $this->email; }
    public function getRol(): string { return $this->rol; }
    public function getActivo(): bool { return $this->activo; }
    public function getAvatar(): ?string { return $this->avatar; }

    public function setId(int $id): void { $this->id = $id; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setRol(string $rol): void { $this->rol = $rol; }
    public function setActivo(bool $activo): void { $this->activo = $activo; }
    public function setAvatar(?string $avatar): void { $this->avatar = $avatar; }

    public function getAll(int $page = 1, int $perPage = 10, string $search = '', string $rolFilter = ''): array {
        $offset = ($page - 1) * $perPage;
        $where = 'WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $where .= ' AND (nombre LIKE :search OR email LIKE :search2)';
            $params[':search'] = "%$search%";
            $params[':search2'] = "%$search%";
        }
        if ($rolFilter !== '') {
            $where .= ' AND rol = :rol';
            $params[':rol'] = $rolFilter;
        }

        $stmt = $this->db->prepare("SELECT id, nombre, email, rol, activo, avatar, ultimo_acceso, created_at FROM usuarios $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(string $search = '', string $rolFilter = ''): int {
        $where = 'WHERE 1=1';
        $params = [];
        if ($search !== '') {
            $where .= ' AND (nombre LIKE :search OR email LIKE :search2)';
            $params[':search'] = "%$search%";
            $params[':search2'] = "%$search%";
        }
        if ($rolFilter !== '') {
            $where .= ' AND rol = :rol';
            $params[':rol'] = $rolFilter;
        }
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios $where");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, nombre, email, rol, activo, avatar, ultimo_acceso, created_at FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(): bool {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)");
        return $stmt->execute([
            ':nombre'   => $this->nombre,
            ':email'    => $this->email,
            ':password' => password_hash($this->password, PASSWORD_BCRYPT),
            ':rol'      => $this->rol ?? 'user',
        ]);
    }

    public function update(): bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol, activo = :activo, avatar = :avatar WHERE id = :id");
        return $stmt->execute([
            ':nombre'  => $this->nombre,
            ':email'   => $this->email,
            ':rol'     => $this->rol,
            ':activo'  => $this->activo,
            ':avatar'  => $this->avatar,
            ':id'      => $this->id,
        ]);
    }

    public function updatePassword(string $newPassword): bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => password_hash($newPassword, PASSWORD_BCRYPT), ':id' => $this->id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updateLastAccess(int $id): void {
        $stmt = $this->db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM usuarios WHERE email = :email";
        $params = [':email' => $email];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params[':id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function validate(): array {
        $errors = [];
        if (empty($this->nombre) || strlen($this->nombre) < 2) $errors[] = 'El nombre debe tener al menos 2 caracteres.';
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
        return $errors;
    }

    public function setResetToken(string $email, string $token, string $expires): bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET reset_token = :token, reset_expires = :expires WHERE email = :email");
        return $stmt->execute([':token' => $token, ':expires' => $expires, ':email' => $email]);
    }

    public function getByResetToken(string $token): ?array {
        $stmt = $this->db->prepare("SELECT id, email FROM usuarios WHERE reset_token = :token AND reset_expires > NOW()");
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function clearResetToken(int $id): void {
        $stmt = $this->db->prepare("UPDATE usuarios SET reset_token = NULL, reset_expires = NULL WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function getByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
