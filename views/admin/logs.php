<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-journal-text me-2 text-warning"></i>Registro de Actividades</h3>
    <span class="badge bg-secondary"><?= $total ?> registros</span>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay registros.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="text-muted"><?= $log['id'] ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                        <td>
                            <?php if ($log['nombre']): ?>
                                <span class="fw-semibold"><?= htmlspecialchars($log['nombre']) ?></span><br>
                                <small class="text-muted"><?= htmlspecialchars($log['email'] ?? '') ?></small>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $colors = [
                                'login'           => 'success',
                                'login_fallido'   => 'danger',
                                'logout'          => 'secondary',
                                'registro'        => 'info',
                                'update_usuario'  => 'primary',
                                'delete_usuario'  => 'danger',
                                'change_password' => 'warning',
                                'reset_password'  => 'warning',
                                'forgot_password' => 'warning',
                            ];
                            $color = $colors[$log['accion']] ?? 'dark';
                            ?>
                            <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($log['accion']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($log['descripcion']) ?></td>
                        <td class="text-muted font-monospace"><?= htmlspecialchars($log['ip']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>?action=logs&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
