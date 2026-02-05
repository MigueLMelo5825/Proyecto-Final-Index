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