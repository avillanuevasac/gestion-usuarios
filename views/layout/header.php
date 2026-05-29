<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php
$auth = new Auth();
$currentUser = $auth->getCurrentUser();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">
            <i class="bi bi-shield-lock-fill me-2"></i>GestiónUsuarios
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <?php if ($currentUser): ?>
                    <?php if ($currentUser['rol'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>?action=usuarios">
                                <i class="bi bi-people-fill me-1"></i>Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>?action=logs">
                                <i class="bi bi-journal-text me-1"></i>Logs
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>?action=perfil">
                            <?php if ($currentUser['avatar']): ?>
                                <img src="<?= ASSETS_URL . htmlspecialchars($currentUser['avatar']) ?>" class="avatar-sm rounded-circle me-1" alt="avatar">
                            <?php else: ?>
                                <i class="bi bi-person-circle me-1"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($currentUser['nombre']) ?>
                            <span class="badge bg-<?= $currentUser['rol'] === 'admin' ? 'danger' : 'secondary' ?> ms-1">
                                <?= $currentUser['rol'] ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm ms-2" href="<?= BASE_URL ?>?action=logout">
                            <i class="bi bi-box-arrow-right me-1"></i>Salir
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>?action=login">Iniciar sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm ms-2" href="<?= BASE_URL ?>?action=register">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
