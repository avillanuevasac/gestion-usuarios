<?php require __DIR__ . '/../layout/header.php'; ?>

<?php if (isset($_SESSION['reset_link'])): ?>
<div class="alert alert-warning border-warning shadow-sm">
    <strong>⚠️ Modo desarrollo — email no enviado</strong><br>
    El servidor SMTP no está configurado. Usa este enlace directamente para restablecer la contraseña:<br>
    <a href="<?= htmlspecialchars($_SESSION['reset_link']) ?>" class="fw-bold text-break">
        <?= htmlspecialchars($_SESSION['reset_link']) ?>
    </a>
</div>
<?php unset($_SESSION['reset_link']); ?>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                    <h4 class="mt-2 fw-bold">Iniciar Sesión</h4>
                </div>
                <form method="POST" action="<?= BASE_URL ?>?action=login" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="admin@sistema.com" required autofocus>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePass">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                        </button>
                    </div>
                </form>
                <hr>
                <div class="text-center small">
                    <a href="<?= BASE_URL ?>?action=register">¿No tienes cuenta? Regístrate</a>
                    &nbsp;·&nbsp;
                    <a href="<?= BASE_URL ?>?action=forgotPassword">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
