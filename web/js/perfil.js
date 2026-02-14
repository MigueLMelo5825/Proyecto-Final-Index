// Detecta la carpeta del proyecto (la primera parte de la ruta después de localhost)
const path = window.location.pathname.split('/');

const nombrePath = path[1]; // Esto tomará el nombre de proyecto que cada uno tenga

const url = nombrePath;

//guardar likes y calificacion
const urlTopTresPeliculas = `/${url}/index.php?ctl=topTresPeliculas`;
const urlTopTresLibros = `/${url}/index.php?ctl=topTresLibros`;


async function cargarTendenciasSidebar(tipo) {
    // Definimos el contenedor y la acción según el tipo
    const esPelicula = tipo === 'pelicula';
    const idContenedor = esPelicula ? 'top-sidebar-peliculas' : 'top-sidebar-libros';
    
    const urlFinal = esPelicula ? urlTopTresPeliculas : urlTopTresLibros;
    
    const contenedor = document.getElementById(idContenedor);
    if (!contenedor) return;

    try {
        // Petición al controlador MVC
        const respuesta = await fetch(urlFinal);
        const datos = await respuesta.json();

        if (datos.status === "success") {
            const items = esPelicula ? datos.peliculas : datos.libros;
            
            if (items.length === 0) {
                contenedor.innerHTML = "<p class='text-muted' style='font-size:12px;'>No hay tendencias aún.</p>";
                return;
            }

            // Limpiamos y generamos el HTML
            contenedor.innerHTML = "";
            
            items.forEach((item, index) => {
                const puesto = index + 1;
                const img = esPelicula ? item.portada : item.imagen_url;
                const subtexto = esPelicula 
                    ? `❤ ${item.total_likes} Likes` 
                    : `★ ${parseFloat(item.promedio).toFixed(1)} / 5`;
                
                // Color del icono según tipo
                const colorIcono = esPelicula ? 'color:#ff4d6d;' : 'color:#ffb703;';

                const html = `
                    <div class="mini-item" onclick="irAFicha('${item.id}', '${tipo}')">
                        <div class="mini-rank-number">${puesto}</div>
                        <img src="${img}" class="mini-img" onerror="this.src='web/img/fallback.png'">
                        <div class="mini-info">
                            <h4 title="${item.titulo}">${item.titulo}</h4>
                            <p style="${colorIcono}">${subtexto}</p>
                        </div>
                    </div>
                `;
                contenedor.insertAdjacentHTML('beforeend', html);
            });
        }
    } catch (error) {
        console.error(`Error cargando sidebar ${tipo}:`, error);
    }
}

// ---------------- REDIRECCIÓN ----------------

function irAFicha(id, type) {
    // Redirige a la ficha técnica
    window.location.href = `/${url}/index.php?ctl=fichaLibroPelicula&id=${id}&type=${type}`;
}

// ---------------- INICIALIZACIÓN ----------------

document.addEventListener("DOMContentLoaded", () => {
    // Cargamos ambos rankings al entrar al perfil
    cargarTendenciasSidebar('pelicula');
    cargarTendenciasSidebar('libro');
});