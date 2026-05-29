<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-check fs-1 text-success"></i>
                    <h4 class="mt-2 fw-bold">Nueva Contraseña</h4>
                </div>
                <form method="POST" action="<?= BASE_URL ?>?action=resetPassword">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••" required minlength="6">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="confirm" id="confirm" class="form-control" placeholder="••••••" required>
                        <div id="matchMsg" class="form-text"></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-1"></i>Restablecer contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
