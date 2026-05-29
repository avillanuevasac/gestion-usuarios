# Sistema de Gestión de Usuarios

Sistema MVC en PHP con MySQL que implementa autenticación, roles, CRUD completo y funcionalidades extra.

## Instalación

1. Clonar/copiar el proyecto en `htdocs/gestion-usuarios` (XAMPP) o `www/gestion-usuarios` (WAMP)
2. Importar `database.sql` en phpMyAdmin o mediante consola:
   ```bash
   mysql -u root -p < database.sql
   ```
3. Ajustar credenciales en `config/Database.php`:
   ```php
   private $host = 'localhost';
   private $db   = 'usuarios_mvc';
   private $user = 'root';
   private $pass = '';
   ```
4. Asegurar que `uploads/avatars/` tenga permisos de escritura:
   ```bash
   chmod -R 755 uploads/
   ```
5. Acceder a `http://localhost/gestion-usuarios/public/`

## Credenciales

| Rol   | Email                | Contraseña |
|-------|----------------------|------------|
| Admin | admin@sistema.com    | password   |
| User  | usuario@test.com     | password   |

## Características

### Core
- Login / Logout con sesiones PHP
- Registro de nuevos usuarios
- CRUD completo de usuarios (admin)
- Roles: `admin` y `user`
- Cambio de contraseña con verificación

### Extra implementado
- **Recuperación de contraseña** — token con expiración de 1 hora, enviado por email
- **Paginación** — 10 usuarios por página en el listado
- **Búsqueda y filtros** — por nombre/email y por rol
- **Avatar de usuario** — subida de imagen (JPG, PNG, GIF, WEBP, máx. 2MB) con preview
- **API REST** — endpoint JSON en `/public/index.php?action=api/usuarios` protegido con token
- **Sistema de logs** — registra login, logout, fallos, creación/edición/eliminación + IP

## Estructura MVC

```
gestion-usuarios/
├── config/Database.php          → Singleton PDO
├── models/
│   ├── Usuario.php              → CRUD + validaciones
│   ├── Auth.php                 → login/logout/sesión
│   └── Log.php                  → registro de actividades
├── controllers/
│   ├── AuthController.php       → login, register, forgot/reset password
│   └── UsuarioController.php    → CRUD, perfil, cambio de pass, API, logs
├── middleware/AuthMiddleware.php → protección de rutas y roles
├── views/
│   ├── layout/{header,footer}   → plantillas base con Bootstrap 5
│   ├── auth/                    → login, register, forgot, reset
│   ├── usuarios/                → index, edit, perfil
│   └── admin/logs.php           → vista de actividad del sistema
├── public/index.php             → Front Controller (router)
├── assets/css/style.css
├── assets/js/app.js
└── uploads/avatars/
```

## API REST

```bash
# Listar usuarios (requiere header X-Api-Token)
curl -H "X-Api-Token: mi-token-secreto-api" \
     http://localhost/gestion-usuarios/public/index.php?action=api/usuarios
```

## Seguridad

- Prepared Statements en todas las consultas → previene SQL Injection
- `htmlspecialchars()` en todas las salidas → previene XSS
- `password_hash()` / `password_verify()` con bcrypt
- Tokens de reset aleatorios con expiración (`bin2hex(random_bytes(32))`)
- Middleware de autenticación y verificación de roles en cada ruta sensible
