//creamos variables para enviar peticion
// Detecta la carpeta del proyecto (la primera parte de la ruta después de localhost)
const path = window.location.pathname.split('/');

const nombrePath = path[1]; // Esto tomará el nombre de proyecto que cada uno tenga

const url = nombrePath;

//guardar likes y calificacion
const urlTopTresPeliculas = `/${url}/index.php?ctl=topTresPeliculas`;

async function mostrarTopTresPeliculas() {

    const contenedor = document.getElementById('contenedor-top-peliculas');
    if (!contenedor) return;

    try {
        
        const response = await fetch(urlTopTresPeliculas);
        const data = await response.json();

        if (data.status === "success" && data.peliculas.length > 0) {
            contenedor.innerHTML = "";

            data.peliculas.forEach((pelicula, index) => {
                const puesto = index + 1;
                const claseEspecial = puesto === 1 ? 'gold-rank' : '';
                
                const html = `
                    <div class="top-card ${claseEspecial}" onclick="irARegistro()">
                        <div class="medalla-puesto">${puesto}</div>
                        <img src="${pelicula.portada}" alt="${pelicula.titulo}">
                        <div class="info-top">
                            <h4>${pelicula.titulo}</h4>
                            <p>❤ ${pelicula.total_likes} Likes</p>
                        </div>
                    </div>
                `;
                contenedor.insertAdjacentHTML('beforeend', html);
            });
        }
    } catch (error) {
        console.error("Error cargando ranking:", error);
        contenedor.innerHTML = "<p>No pudimos cargar el ranking en este momento.</p>";
    }
}

// Función para redirigir a la ficha técnica
function irARegistro() {
    window.location.href = `/${url}/index.php?ctl=registro`;
}

window.onload = function(){
    mostrarTopTresPeliculas();
}