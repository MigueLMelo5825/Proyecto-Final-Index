<?php include_once __DIR__.'/header.php'; ?>

<form method="POST">
    <label>Nombre de la lista</label>
    <input type="text" name="nombre" required>

    <label>Descripción</label>
    <textarea name="descripcion"></textarea>
<label>Tipo de lista:</label>
<select name="tipo">
    <option value="libro">Solo libros</option>
    <option value="pelicula">Solo películas</option>
    <option value="mixta" selected>Mixta</option>
</select>


    <button type="submit">Crear lista</button>
</form>

<?php include_once __DIR__.'/footer.php'; ?>
