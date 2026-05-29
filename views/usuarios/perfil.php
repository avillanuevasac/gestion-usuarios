<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <h3 class="fw-bold mb-4"><i class="bi bi-person-badge me-2 text-primary"></i>Mi Perfil</h3>

        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <?php if ($user['avatar']): ?>
                            <img src="<?= ASSETS_URL . htmlspecialchars($user['avatar']) ?>" class="avatar-xl rounded-circle mb-3" alt="avatar">
                        <?php else: ?>
                            <div class="avatar-placeholder-xl mb-3">
                                <i class="bi bi-person-fill fs-1"></i>
                            </div>
                        <?php endif; ?>
                        <h5 class="fw-bold"><?= htmlspecialchars($user['nombre']) ?></h5>
                        <span class="badge bg-<?= $user['rol'] === 'admin' ? 'danger' : 'secondary' ?> mb-2">
                            <?= $user['rol'] ?>
                        </span>
                        <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                        <hr class="w-100">
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Registrado: <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </small>
                        <small class="text-muted mt-1">
                            <i class="bi bi-clock me-1"></i>Último acceso:
                            <?= $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : '—' ?>
                        </small>
                        <a href="<?= BASE_URL ?>?action=edit&id=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm mt-3 w-100">
                            <i class="bi bi-pencil me-1"></i>Editar perfil
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-lock me-2"></i>Cambiar Contraseña</h5>
                        <form method="POST" action="<?= BASE_URL ?>?action=changePassword" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Contraseña actual</label>
                                <input type="password" name="current_password" class="form-control" placeholder="••••••" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" name="new_password" id="password" class="form-control" placeholder="••••••" required minlength="6">
                                <div class="password-strength mt-1" id="strengthBar"></div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" name="confirm_password" id="confirm" class="form-control" placeholder="••••••" required>
                                <div id="matchMsg" class="form-text"></div>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-shield-lock me-1"></i>Actualizar contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
