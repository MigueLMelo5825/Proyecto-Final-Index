<?php include_once __DIR__.'/../Models/header.php'; ?>

<form method="POST">
    <label>Nombre de la lista</label>
    <input type="text" name="nombre" required>

    <label>Descripción</label>
    <textarea name="descripcion"></textarea>

    <label>Tipo</label>
    <select name="tipo">
        <option value="libro">Libros</option>
        <option value="pelicula">Películas</option>
    </select>

    <button type="submit">Crear lista</button>
</form>

<?php include_once __DIR__.'/../Models/footer.php'; ?>