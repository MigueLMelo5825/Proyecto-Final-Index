//obtenemos los valores globales
const valores = new URLSearchParams(window.location.search);
const idLibroPelicula = valores.get('id');
const tipo = valores.get('type');

//se crea una url diamina que se pueda usar en todos los proyectos -- Miguel Melo
// Detecta la carpeta del proyecto (la primera parte de la ruta después de localhost)
const pathSegments = window.location.pathname.split('/');

const nombreProyecto = pathSegments[1]; // Esto tomará el nombre de proyecto que cada uno tenga

const baseUrl = nombreProyecto;

const urlPhp = `${baseUrl}/index.php?ctl=guardarLikeYComentario`;


//obtenemos los botones e inputs del DOM
const like = document.getElementById("btn-favorito");
//funcion para enviar like y guardarlo
async function enviarLike(){
    
    like.addEventListener("click", async event =>{
        event.preventDefault();

        try{

            const peticionJson = await fetch (urlPhp, {
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

