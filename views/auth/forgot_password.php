<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-key-fill fs-1 text-warning"></i>
                    <h4 class="mt-2 fw-bold">Recuperar Contraseña</h4>
                    <p class="text-muted small">Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.</p>
                </div>
                <form method="POST" action="<?= BASE_URL ?>?action=forgotPassword">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required autofocus>
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="bi bi-send me-1"></i>Enviar enlace
                        </button>
                    </div>
                </form>
                <hr>
                <div class="text-center small">
                    <a href="<?= BASE_URL ?>?action=login">Volver al login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
