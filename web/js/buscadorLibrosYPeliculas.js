// ---------------- CONFIGURACIÓN ----------------

//se crea una url diamina que se pueda usar en todos los proyectos -- Miguel Melo
// Detecta la carpeta del proyecto (la primera parte de la ruta después de localhost)
const pathSegments = window.location.pathname.split('/');

const nombreProyecto = pathSegments[1]; // Esto tomará el nombre de proyecto que cada uno tenga

const baseUrl = nombreProyecto;

// URL del backend del buscador (controlador MVC)
const urlPhp = `/${baseUrl}/index.php?ctl=buscar`;

// Imagen fallback (asegúrate de que existe en /Proyecto/web/img/)
const fallback = `/${baseUrl}/web/img/fallback.png`;

// Redirección a la ficha (AJUSTA si tienes otra ruta)
const urlRedireccion = `/${baseUrl}/index.php?ctl=fichaLibroPelicula`;

// Elementos del DOM
const inputLibro = document.getElementById("inputLibro");
const divEncontrados =
    document.getElementById("libroOPeliculaEncontrada") ||
    document.getElementById("resultadosBusqueda");

const cache = {};


// ---------------- INICIALIZACIÓN ----------------

document.addEventListener("DOMContentLoaded", () => {
    if (inputLibro && divEncontrados) {
        mostrarLibroPelicula();
    }
});


// ---------------- EVENTO PRINCIPAL ----------------

function mostrarLibroPelicula() {
    inputLibro.addEventListener("keyup", event => {
        event.preventDefault();

        const texto = inputLibro.value.trim();

        if (texto.length > 0) {
            divEncontrados.style.display = "block";
            cargarLibroPelicula(texto);
        } else {
            divEncontrados.innerHTML = "";
            divEncontrados.style.display = "none";
        }
    });
}


// ---------------- FETCH AL BACKEND ----------------

async function cargarLibroPelicula(texto) {
    try {
        const peticion = await fetch(`${urlPhp}&texto=${encodeURIComponent(texto)}`);
        const datos = await peticion.json();

        divEncontrados.innerHTML = "";

        if (!Array.isArray(datos) || datos.length === 0) {
            divEncontrados.innerHTML = "<p>No se encontraron resultados</p>";
            return;
        }

        const lista = datos.map(item => ({
            id: item.id,
            titulo: item.titulo,
            info_extra: item.info_extra,
            genero: item.genero,
            imagen_url: item.imagen_url,
            type: item.tipo
        }));

        lista.crearLista();

    } catch (error) {
        console.error("Error en el fetch:", error);
    }
}


// ---------------- CREAR LISTA ----------------

Array.prototype.crearLista = function () {
    divEncontrados.innerHTML = "";

    const ul = document.createElement("div");
    ul.className = "list-group w-100";

    this.forEach(item => {
        const li = document.createElement("a");
        li.className = "list-group-item list-group-item-action d-flex align-items-center";
        li.dataset.id = item.id;
        li.dataset.type = item.type;

        const img = document.createElement("img");
        img.src = item.imagen_url ? item.imagen_url.replace("http://", "https://") : fallback;
        img.style.width = "45px";
        img.style.height = "65px";
        img.style.objectFit = "cover";
        img.className = "me-3 rounded";

        const divTexto = document.createElement("div");

        const pTitulo = document.createElement("p");
        pTitulo.innerHTML = `<strong>${item.titulo}</strong>`;

        const pExtra = document.createElement("p");
        pExtra.className = "mb-0 text-muted";
        pExtra.textContent = item.type === "libro"
            ? "Autor: " + (item.info_extra || "Desconocido")
            : "Año: " + (item.info_extra || "Desconocido");

        const pGenero = document.createElement("p");
        pGenero.className = "mb-0 text-muted";
        pGenero.textContent = "Género: " + (item.genero || "N/A");

        divTexto.appendChild(pTitulo);
        divTexto.appendChild(pExtra);
        divTexto.appendChild(pGenero);

        li.appendChild(img);
        li.appendChild(divTexto);
        ul.appendChild(li);

        // HOVER
        li.addEventListener("mouseover", () => li.style.background = "#f8f9fa");
        li.addEventListener("mouseout", () => li.style.background = "white");

        // CLICK
        li.addEventListener("click", () => seleccionarLibro(li));
    });

    divEncontrados.appendChild(ul);
};


// ---------------- REDIRECCIÓN ----------------

function seleccionarLibro(li) {
    const id = li.dataset.id;
    const type = li.dataset.type;

    if (!id || !type) return;

    const url = `${urlRedireccion}&id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}`;
    window.location.href = url;
}
