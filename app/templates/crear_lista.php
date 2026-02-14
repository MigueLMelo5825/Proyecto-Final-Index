<?php include_once __DIR__ . '/header.php'; ?>
<link rel="stylesheet" href="web/css/stylePerfil.css">
<link rel="stylesheet" href="web/css/nuevaListaStyle.css">

<div class="perfil-page nueva-lista">

    <!-- Header -->
    <div class="perfil-header nueva-lista-header">
        <div class="perfil-container">
            <div class="perfil-info">
                <h2>Crear Nueva Lista</h2>
                <p class="bio">Organiza tus libros y películas favoritas en tu perfil.</p>
            </div>
        </div>
    </div>

    <!-- Contenedor compacto horizontal -->
    <div class="nueva-lista-container">

        <!-- Formulario -->
        <div class="card nueva-lista-card">
            <form method="POST" class="nueva-lista-form">

                <div class="form-group">
                    <label>Nombre de la lista</label>
                    <input type="text" name="nombre" required placeholder="Nombre de tu lista">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                 
                    <textarea name="descripcion" placeholder="Agrega una descripción"></textarea>

                </div>

                <div class="form-group">
                    <label>Tipo de lista</label>
                    <select name="tipo">
                        <option value="libro">Solo libros</option>
                        <option value="pelicula">Solo películas</option>
                        <option value="mixta" selected>Mixta</option>   
                    </select>
                </div>

                <button type="submit" class="btn-primary btn-crear">Crear lista</button>
            </form>
        </div>

        <!-- Bloque lateral compacto -->
        <div class="nueva-lista-side">
            <h3>Inspírate</h3>
            <p>Crea listas temáticas, por género o por tu estado de ánimo. Mantén todo organizado en tu perfil.</p>
        </div>

    </div>

</div>

<?php include_once __DIR__ . '/footer.php'; ?>
