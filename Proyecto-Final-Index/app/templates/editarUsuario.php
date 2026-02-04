<?php require __DIR__ . '/header.php'; ?>

<h1>Editar Usuario</h1>

<form method="post" action="index.php?ctl=guardarEdicion" style="max-width: 400px;">

    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= $usuario['email'] ?>" required>

    <label>País:</label>
    <input type="text" name="pais" value="<?= $usuario['pais'] ?>" required>

    <label>Rol:</label>
    <select name="rol">
        <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <button type="submit" style="margin-top: 15px;">Guardar cambios</button>
</form>

<a href="index.php?ctl=panelAdmin" style="display:block; margin-top:20px;">Volver al panel</a>

<?php require __DIR__ . '/footer.php'; ?>
