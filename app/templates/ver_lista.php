<h2><?= htmlspecialchars($lista['nombre']) ?></h2>
<p><?= htmlspecialchars($lista['descripcion']) ?></p>

<h3>Elementos de la lista</h3>

<?php if (empty($items)): ?>
    <p>No hay elementos en esta lista.</p>
<?php else: ?>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <strong><?= htmlspecialchars($item['titulo']) ?></strong><br>
                <?= htmlspecialchars($item['descripcion']) ?><br>
                <small><?= $item['creado_en'] ?></small>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<h3>Añadir elemento a esta lista</h3>

<!-- USAMOS LOS MISMOS IDs QUE EL BUSCADOR DEL HEADER -->
<div class="mb-3 position-relative" style="max-width: 400px;">
    <input class="form-control" type="search" placeholder="Buscar libros o películas..." id="inputLibro">

    <!-- MISMO ID QUE EL HEADER PARA REUTILIZAR CSS -->
    <div id="resultadosBusqueda"
         class="list-group position-absolute w-100 mt-1"
         style="z-index: 2000; display:none;"></div>
</div>

<script>
// --- CONFIG ---
const input = document.getElementById("inputLibro");
const contenedor = document.getElementById("resultadosBusqueda");
const idLista = <?= $lista['id'] ?>;

// URL del backend del buscador (igual que en tu JS original)
const urlBuscar = "index.php?ctl=buscar";

// Imagen fallback
const fallback = "web/img/fallback.png";

// --- EVENTO PRINCIPAL ---
input.addEventListener("keyup", async () => {
    const texto = input.value.trim();

    contenedor.innerHTML = "";
    if (texto.length < 2) {
        contenedor.style.display = "none";
        return;
    }

    const peticion = await fetch(`${urlBuscar}&texto=${encodeURIComponent(texto)}`);
    const datos = await peticion.json();

    contenedor.innerHTML = "";
    contenedor.style.display = "block";

    if (!Array.isArray(datos) || datos.length === 0) {
        contenedor.innerHTML = "<div class='list-group-item'>Sin resultados</div>";
        return;
    }

    datos.forEach(item => {
        const div = document.createElement("div");
        div.className = "list-group-item d-flex align-items-center justify-content-between";

        const img = item.imagen_url
            ? item.imagen_url.replace("http://", "https://")
            : fallback;

        div.innerHTML = `
            <div class="d-flex align-items-center">
                <img src="${img}" width="45" height="65" class="me-3 rounded" style="object-fit:cover;">
                <div>
                    <strong>${item.titulo}</strong><br>
                    <small>${item.type === "libro" ? "Autor: " + item.info_extra : "Año: " + item.info_extra}</small><br>
                    <small class="text-muted">Género: ${item.genero}</small>
                </div>
            </div>

<form action="index.php?ctl=lista&accion=añadir" method="POST">


                <input type="hidden" name="id_lista" value="${idLista}">
                ${item.type === "libro"
                    ? `<input type="hidden" name="id_libro" value="${item.id}">`
                    : `<input type="hidden" name="id_pelicula" value="${item.id}">`
                }
                <button class="btn btn-primary btn-sm">Añadir</button>
            </form>
        `;

        contenedor.appendChild(div);
    });
});
</script>
