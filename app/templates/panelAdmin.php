<?php require __DIR__ . '/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Panel de Administración</h1>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Nivel</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['nivel'] ?></td>

                <td>
                    <a href="index.php?ctl=cambiarRol&id=<?= $u['id'] ?>" 
                       class="btn btn-warning btn-sm">
                        Cambiar rol
                    </a>

                    <a href="index.php?ctl=eliminarUsuario&id=<?= $u['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">
                        Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/footer.php'; ?>
