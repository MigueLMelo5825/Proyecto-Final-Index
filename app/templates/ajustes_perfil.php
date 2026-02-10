<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ajustes del perfil</title>

    <link rel="stylesheet" href="web/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="web/css/style.css">

    <style>
        .top-item img {
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
        }

        .filtro-input {
            max-width: 350px;
        }

        .oculto {
            display: none !important;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . "/header.php"; ?>

    <main class="container py-5">

        <h2 class="mb-4">Ajustes del perfil</h2>

        <!-- FOTO DE PERFIL -->
        <div class="card mb-4">
            <div class="card-body">
                <h4>Cambiar foto de perfil</h4>

                <form method="POST" action="index.php?ctl=guardarFotoPerfil" enctype="multipart/form-data">
                    <input type="file" name="foto" class="form-control mb-3" accept="image/*" required>
                    <button class="btn btn-primary">Guardar foto</button>
                </form>
            </div>
        </div>

        <!-- BIO -->
        <div class="card mb-4">
            <div class="card-body">
                <h4>Editar biografía</h4>

                <form action="index.php?ctl=guardarBio" method="POST">
                    <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($usuario['bio'] ?? '') ?></textarea>
                    <button class="btn btn-primary mt-3">Guardar cambios</button>
                </form>
            </div>
        </div>

        <!-- TOP 4 LIBROS -->
        <div class="card mb-4">
            <div class="card-body">
                <h4>Top 4 libros</h4>
                <p>Busca y selecciona tus libros favoritos (máx. 4).</p>

                <input type="text" id="buscarLibros" class="form-control filtro-input mb-3" placeholder="Buscar libro...">

                <form action="index.php?ctl=guardarTopLibros" method="POST">
                    <div class="row g-3" id="contenedorLibros">

                        <?php foreach ($todosLibros as $libro): ?>
                            <div class="col-6 col-md-3 libro-item"
                                data-titulo="<?= strtolower($libro['titulo']) ?>">
                                <label class="d-block text-center">
                                    <input type="checkbox" name="top_libros[]" value="<?= $libro['id'] ?>"
                                        <?= in_array($libro['id'], $topLibrosIds) ? 'checked' : '' ?>>
                                    <img src="<?= $libro['imagen_url'] ?>" class="img-fluid rounded">
                                    <small><?= htmlspecialchars($libro['titulo']) ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>

                    </div>

                    <button class="btn btn-primary mt-3">Guardar Top 4</button>
                </form>
            </div>
        </div>

        <!-- TOP 4 PELÍCULAS -->
        <div class="card mb-4">
            <div class="card-body">
                <h4>Top 4 películas</h4>
                <p>Busca y selecciona tus películas favoritas (máx. 4).</p>

                <input type="text" id="buscarPeliculas" class="form-control filtro-input mb-3" placeholder="Buscar película...">

                <form action="index.php?ctl=guardarTopPeliculas" method="POST">
                    <div class="row g-3" id="contenedorPeliculas">

                        <?php foreach ($todasPeliculas as $peli): ?>
                            <div class="col-6 col-md-3 peli-item"
                                data-titulo="<?= strtolower($peli['titulo']) ?>">
                                <label class="d-block text-center">
                                    <input type="checkbox" name="top_peliculas[]" value="<?= $peli['id'] ?>"
                                        <?= in_array($peli['id'], $topPeliculasIds) ? 'checked' : '' ?>>
                                    <img src="<?= $peli['portada'] ?>" class="img-fluid rounded">
                                    <small><?= htmlspecialchars($peli['titulo']) ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>

                    </div>

                    <button class="btn btn-primary mt-3">Guardar Top 4</button>
                </form>
            </div>
        </div>

    </main>

    <?php include __DIR__ . "/footer.php"; ?>

    <script>
        function configurarFiltro(inputId, itemClass, contenedorId) {
            const input = document.getElementById(inputId);
            const contenedor = document.getElementById(contenedorId);
            const items = Array.from(document.querySelectorAll("." + itemClass));

            function ordenarYFiltrar() {
                const filtro = input.value.toLowerCase();

                const seleccionados = [];
                const noSeleccionados = [];

                items.forEach(item => {
                    const checkbox = item.querySelector("input[type='checkbox']");
                    if (checkbox.checked) {
                        seleccionados.push(item);
                    } else {
                        noSeleccionados.push(item);
                    }
                });

                contenedor.innerHTML = "";

                seleccionados.forEach(item => {
                    item.classList.remove("oculto");
                    contenedor.appendChild(item);
                });

                noSeleccionados.forEach(item => {
                    const titulo = item.dataset.titulo;
                    if (filtro.length > 0 && titulo.includes(filtro)) {
                        item.classList.remove("oculto");
                        contenedor.appendChild(item);
                    } else {
                        item.classList.add("oculto");
                        contenedor.appendChild(item);
                    }
                });
            }

            input.addEventListener("input", ordenarYFiltrar);
            ordenarYFiltrar();
        }

        configurarFiltro("buscarLibros", "libro-item", "contenedorLibros");
        configurarFiltro("buscarPeliculas", "peli-item", "contenedorPeliculas");
    </script>

</body>

</html>