<?php require __DIR__ . '/header.php'; ?>

<h1>Panel de Administración</h1>

<!-- BUSCADOR EN TIEMPO REAL -->
<input type="text" id="buscador" placeholder="Buscar usuario..." style="margin-bottom: 20px;">

<!-- TABLA DE USUARIOS -->
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>País</th>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['nombre'] ?></td>
            <td><?= $u['email'] ?></td>
            <td><?= $u['pais'] ?></td>

            <td>
                <!-- FORMULARIO PARA CAMBIAR ROL -->
                <form method="post" action="index.php?ctl=cambiarRol">
                    <input type="hidden" name="id_usuario" value="<?= $u['id'] ?>">

                    <select name="rol" onchange="this.form.submit()">
                        <option value="usuario" <?= $u['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                        <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </form>
            </td>

            <td>
                <a href="index.php?ctl=editarUsuario&id=<?= $u['id'] ?>">Editar</a> |
                <a href="index.php?ctl=eliminarUsuario&id=<?= $u['id'] ?>"
                   onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">
                   Eliminar
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- SCRIPT DEL BUSCADOR -->
<script>
document.getElementById("buscador").addEventListener("keyup", function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("table tr");

    filas.forEach((fila, index) => {
        if (index === 0) return; // saltar cabecera

        let texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? "" : "none";
    });
});
</script>

<?php require __DIR__ . '/footer.php'; ?>
