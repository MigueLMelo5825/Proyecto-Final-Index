//obtenemos los valores globales
const valores = new URLSearchParams(window.location.search);
const idLibroPelicula = valores.get('id');
const tipo = valores.get('type');

//obtenemos los botones e inputs del DOM
const like = document.getElementById("btn-favorito");
//funcion para enviar like y guardarlo
async function enviarLike(){
    like.addEventListener("click", async event =>{
        event.preventDefault();
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