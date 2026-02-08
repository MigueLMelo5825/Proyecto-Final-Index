//obtenemos los valores globales
const valores = new URLSearchParams(window.location.search);
const idLibroPelicula = valores.get('id');
const tipo = valores.get('type');
const promedio = document.getElementById('calificacion');

//creamos variables para enviar peticion
// Detecta la carpeta del proyecto (la primera parte de la ruta después de localhost)
const path = window.location.pathname.split('/');

const nombrePath = path[1]; // Esto tomará el nombre de proyecto que cada uno tenga

const url = nombrePath;

const urlLikeCalificacion = `/${url}/index.php?ctl=guardarLikesYCalificacion`;


//obtenemos los botones e inputs del DOM
const like = document.getElementById("btn-favorito");
const inputsEstrellas = document.querySelectorAll('.estrellas-voto input');
const contadorLikes = document.getElementById('contador-likes');


//funcion para enviar like y guardarlo
async function enviarLike(){
    
    like.addEventListener("click", async event =>{
        event.preventDefault();

        try{

            const peticionJson = await fetch (urlLikeCalificacion, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' 
                },    
                body: JSON.stringify({
                    id: idLibroPelicula,
                    type: tipo,
                    accion: "like",

                })
            });
    
            const datos = await peticionJson.json();

            // 2. Lógica visual: Si el PHP dice 'agregado', pintamos el corazón
            if (datos.status === "success") {
                if (datos.resultado === "agregado") {
                    like.classList.add("active");

                    if (contadorLikes && typeof datos.nuevoTotal !== 'undefined') {
                        contadorLikes.textContent = datos.nuevoTotal;
                    }
                } else {
                    like.classList.remove("active");
                }
            } else {
                alert(datos.mensaje); // "Debes iniciar sesión"
            }
            
        }catch(error){
            console.error(error)
        }
    })
}

async function agregarValoracion(){
    
    inputsEstrellas.forEach(estrellas => {
        estrellas.addEventListener('change', async () => {
            const puntuacion = estrellas.value;
        
            try {
                const response = await fetch(urlLikeCalificacion, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: idLibroPelicula,
                        type: tipo,
                        accion: 'calificar',
                        puntuacion: puntuacion
                    })
                });
                
                const peticion = await response.json();
                
                if (peticion.status === 'success') {
                    // AQUÍ LA MAGIA: Actualizamos el texto del promedio sin recargar
                    if (promedio && peticion.nuevoPromedio) {
                        promedio.textContent = `${peticion.nuevoPromedio} de 5`;
                        console.log("Promedio actualizado: " + peticion.nuevoPromedio);
                    }
                } else {
                    console.log("Error: " + peticion.mensaje);
                    estrellas.checked = false;
                    // Si el error es de sesión, se podría redirigir:
                    // window.location.href = 'index.php?ctl=login';
                }
            } catch (error) {
                console.error("Error al calificar:", error);
            }
        });
    });
}


//funcion para dar al boton leer mas en descripcion
document.addEventListener("DOMContentLoaded", () => {
    const contenedor = document.getElementById('descripcion-texto');
    const btn = document.getElementById('btn-leer-mas');

    // 1. Si el texto es corto, ocultamos el botón de entrada
    if (contenedor.scrollHeight <= contenedor.offsetHeight) {
        btn.style.display = 'none';
    }

    // 2. Evento de clic
    btn.addEventListener('click', () => {
        const estaExpandido = contenedor.classList.toggle('expandido');
        
        // Cambiamos el texto del botón
        btn.textContent = estaExpandido ? 'Leer menos' : 'Leer más';
        
        // Si colapsamos, volvemos arriba de la descripción suavemente
        if (!estaExpandido) {
            contenedor.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
});

window.onload = function (){
    enviarLike();
    agregarValoracion();
}