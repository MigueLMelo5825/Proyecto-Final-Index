// ---------------- CONFIGURACIÓN ----------------

// Tu proyecto está en: http://localhost/Proyecto/
//const baseUrl = "/Proyecto";

//se crea una url diamina que se pueda usar en todos los proyectos -- Miguel Melo
// Detecta la carpeta del proyecto (la primera parte de la ruta después de localhost)
const pathSegments = window.location.pathname.split('/');

const nombreProyecto = pathSegments[1]; // Esto tomará el nombre de proyecto que cada uno tenga

const baseUrl = nombreProyecto;

// URL del backend del buscador
const urlPhp = `/${baseUrl}/index.php?ctl=buscar`;

// Imagen fallback
const fallback = `/${baseUrl}/web/img/fallback.png`;

// Redirección a la ficha
const urlRedireccion = `/${baseUrl}/index.php?ctl=fichaLibroPelicula`;

// Elementos del DOM
const inputLibro = document.getElementById("inputLibro");
const divEncontrados =
    document.getElementById("libroOPeliculaEncontrada") ||
    document.getElementById("resultadosBusqueda");

const cache = {};
let indexSeleccionado = -1;
let timeoutBusqueda;


// ---------------- INICIALIZACIÓN ----------------

document.addEventListener("DOMContentLoaded", () => {
    if (inputLibro && divEncontrados) {
        mostrarLibroPelicula();
    }
});


// ---------------- EVENTO PRINCIPAL ----------------

function mostrarLibroPelicula() {
    //hacemos uso de la funcion teclado para determinar que realizar segun la peticion dentro del buscador
    inputLibro.addEventListener("keyup", funcionesTeclado);

    //cerramos la ventana si el usuario da click fuera del buscador
    document.addEventListener("click", (e) => {
        if (!inputLibro.contains(e.target) && !divEncontrados.contains(e.target)) {
            cerrarBuscador();
        }
    });

    // reabrimos el buscador si tiene texto en el input
    inputLibro.addEventListener("focus", () => {
        if (inputLibro.value.trim().length > 0) {
            divEncontrados.style.display = "block";
        }
    });
}

//se crea las funciones para interpretar la navegacion del usuario dentro del buscador
function funcionesTeclado(e) {

    const texto = inputLibro.value.trim();
    const items = divEncontrados.querySelectorAll(".list-group-item");

    if (items.length > 0) {
        // Lógica para flechas y Enter
        if (e.key === "ArrowDown") {
            indexSeleccionado = (indexSeleccionado + 1) % items.length;
            resaltarItem(items);
            return;
        } 
        if (e.key === "ArrowUp") {
            indexSeleccionado = (indexSeleccionado - 1 + items.length) % items.length;
            resaltarItem(items);
            return;
        }
        if (e.key === "Enter") {

            e.preventDefault(); 
            
            if (indexSeleccionado > -1 && items[indexSeleccionado]) {
                items[indexSeleccionado].click();
            }
            return;
        }
    }
        
    if (e.key === "Escape") {
        cerrarBuscador();
        return;
    }
    
    // Lógica para que el buscador tarde un poco, y no muestre resultados inmediatamente
    clearTimeout(timeoutBusqueda);
    if (texto.length > 0) {
        timeoutBusqueda = setTimeout(() => {
            if (cache[texto]) {
                cache[texto].crearLista(); //creamos la lista desde el cache guardado
            } else {
                cargarLibroPelicula(texto);
            }
        }, 200);
    } else {
        cerrarBuscador();
    }
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

        // --- GUARDAMOS EN EL CACHÉ ---
        cache[texto] = lista;

        lista.crearLista();

    } catch (error) {
        console.error("Error en el fetch:", error);
    }
}


// ---------------- CREAR LISTA ----------------

Array.prototype.crearLista = function () {

    indexSeleccionado = -1; //reseteamos el index del buscador
    divEncontrados.innerHTML = "";
    divEncontrados.style.display = "block";

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
    console.log("Redirigiendo a:", url);
    window.location.href = url;
}

//funcion para resaltas los libros y peliculas al navegar
function resaltarItem(items) {
    items.forEach((item, i) => {
        if (i === indexSeleccionado) {
            item.classList.add("active");
            item.scrollIntoView({ block: "nearest" });
        } else {
            item.classList.remove("active");
        }
    });
}

// funcion para cerrar el buscador
function cerrarBuscador() {
    divEncontrados.innerHTML = "";
    divEncontrados.style.display = "none";
    indexSeleccionado = -1;
}