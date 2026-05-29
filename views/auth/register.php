<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus-fill fs-1 text-success"></i>
                    <h4 class="mt-2 fw-bold">Crear Cuenta</h4>
                </div>
                <form method="POST" action="<?= BASE_URL ?>?action=register" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" required minlength="2">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña <small class="text-muted">(mín. 6 caracteres)</small></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••" required minlength="6">
                        </div>
                        <div class="password-strength mt-1" id="strengthBar"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirmar contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="confirm" id="confirm" class="form-control" placeholder="••••••" required>
                        </div>
                        <div id="matchMsg" class="form-text"></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-person-check me-1"></i>Registrarse
                        </button>
                    </div>
                </form>
                <hr>
                <div class="text-center small">
                    <a href="<?= BASE_URL ?>?action=login">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
