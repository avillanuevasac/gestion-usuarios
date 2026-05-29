<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="d-flex align-items-center mb-4">
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Usuario</h4>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="<?= BASE_URL ?>?action=update&id=<?= $user['id'] ?>" enctype="multipart/form-data" novalidate>

                    <div class="text-center mb-4">
                        <?php if ($user['avatar']): ?>
                            <img src="<?= ASSETS_URL . htmlspecialchars($user['avatar']) ?>" class="avatar-lg rounded-circle mb-2" alt="avatar" id="avatarPreview">
                        <?php else: ?>
                            <div class="avatar-placeholder-lg mx-auto mb-2" id="avatarPlaceholder">
                                <i class="bi bi-person-fill fs-1"></i>
                            </div>
                            <img src="" class="avatar-lg rounded-circle mb-2 d-none" alt="avatar" id="avatarPreview">
                        <?php endif; ?>
                        <div>
                            <label class="btn btn-outline-secondary btn-sm" for="avatarInput">
                                <i class="bi bi-camera me-1"></i>Cambiar foto
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                        </div>
                        <small class="text-muted">JPG, PNG, GIF, WEBP — máx. 2MB</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre']) ?>" required minlength="2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <?php if ($currentUser['rol'] === 'admin'): ?>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Rol</label>
                            <select name="rol" class="form-select">
                                <option value="user"  <?= $user['rol'] === 'user'  ? 'selected' : '' ?>>Usuario</option>
                                <option value="admin" <?= $user['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Estado</label>
                            <select name="activo" class="form-select">
                                <option value="1" <?= $user['activo'] ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= !$user['activo'] ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-floppy me-1"></i>Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
