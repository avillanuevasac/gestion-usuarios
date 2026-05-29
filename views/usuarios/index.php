<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Usuarios</h3>
    <a href="<?= BASE_URL ?>?action=register" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Nuevo usuario
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>" class="row g-2 align-items-end">
            <input type="hidden" name="action" value="usuarios">
            <div class="col-md-5">
                <label class="form-label small text-muted">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Nombre o email..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Filtrar por rol</label>
                <select name="rol" class="form-select">
                    <option value="">Todos los roles</option>
                    <option value="admin" <?= $rolFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user"  <?= $rolFilter === 'user'  ? 'selected' : '' ?>>Usuario</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
            </div>
            <div class="col-md-2">
                <a href="<?= BASE_URL ?>?action=usuarios" class="btn btn-outline-secondary w-100">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Avatar</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último acceso</th>
                    <th>Creado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No se encontraron usuarios.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="text-muted small"><?= $u['id'] ?></td>
                        <td>
                            <?php if ($u['avatar']): ?>
                                <img src="<?= ASSETS_URL . htmlspecialchars($u['avatar']) ?>" class="avatar-sm rounded-circle" alt="avatar">
                            <?php else: ?>
                                <div class="avatar-placeholder"><i class="bi bi-person-fill"></i></div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($u['nombre']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= $u['rol'] === 'admin' ? 'danger' : 'secondary' ?>">
                                <?= $u['rol'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $u['activo'] ? 'success' : 'warning' ?>">
                                <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : '—' ?></td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-center">
                            <a href="<?= BASE_URL ?>?action=edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                            onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>', '<?= BASE_URL ?>')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
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
                <a class="page-link" href="<?= BASE_URL ?>?action=usuarios&page=<?= $i ?>&search=<?= urlencode($search) ?>&rol=<?= $rolFilter ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<p class="text-muted small text-end mt-2">Total: <?= $total ?> usuario(s)</p>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar al usuario <strong id="deleteUserName"></strong>? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteLink" href="#" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
