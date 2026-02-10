<?php include_once __DIR__ . '/header.php'; ?>

<div class="container mt-5">
    <h2>Explorar usuarios</h2>

    <form method="GET" action="index.php" class="mt-3 mb-4">
        <input type="hidden" name="ctl" value="buscarUsuarios">
        <input type="text" name="email" class="form-control" placeholder="Buscar por email...">
    </form>

    <?php if (!empty($usuarios)): ?>
        <ul class="list-group">
            <?php foreach ($usuarios as $u): ?>
                <li class="list-group-item d-flex align-items-center">
                    <img src="<?= $u['foto'] ?>" width="40" height="40" class="rounded-circle me-3">
                    <div>
                        <a href="index.php?ctl=perfil&id=<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['nombre']) ?>
                        </a>
                        <br>
                        <small><?= htmlspecialchars($u['email']) ?></small>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($termino !== ''): ?>
        <p>No se encontraron usuarios con ese email.</p>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>
