<?php
class Database
{
    private static $instance = null;
    private $conn;

    // Colocar las credenciales de la base de datos aquí
    private $host = 'localhost';
    private $db   = 'usuarios_mvc';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
