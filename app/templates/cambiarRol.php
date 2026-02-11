<?php require __DIR__ . '/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Cambiar rol de <?= htmlspecialchars($usuario['username']) ?></h1>

    <form method="POST" action="index.php?ctl=guardarRol" class="col-md-6">

        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

        <label for="nivel" class="form-label">Seleccionar nuevo nivel:</label>
        <select name="nivel" id="nivel" class="form-select">
            <option value="1" <?= $usuario['nivel'] == 1 ? 'selected' : '' ?>>Usuario</option>
            <option value="3" <?= $usuario['nivel'] == 3 ? 'selected' : '' ?>>Administrador</option>
        </select>

        <button class="btn btn-primary mt-3">Guardar cambios</button>
        <a href="index.php?ctl=panelAdmin" class="btn btn-secondary mt-3">Volver</a>
    </form>
</div>

<?php require __DIR__ . '/footer.php'; ?>
