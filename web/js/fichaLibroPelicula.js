//obtenemos los valores globales
const valores = new URLSearchParams(window.location.search);
const idLibroPelicula = valores.get('id');
const tipo = valores.get('type');


//creamos variables para enviar peticion
// Detecta la carpeta del proyecto (la primera parte de la ruta después de localhost)
const path = window.location.pathname.split('/');

const nombrePath = path[1]; // Esto tomará el nombre de proyecto que cada uno tenga

const url = nombrePath;

//guardar likes y calificacion
const urlLikeCalificacion = `/${url}/index.php?ctl=guardarLikesYCalificacion`;

//guardar comentario, modificar y eliminar
const urlComentario = `/${url}/index.php?ctl=guardarComentario`;
const urlEliminarComentario = `/${url}/index.php?ctl=eliminarComentario`;
const urlFotoUsuario = `/${url}/`;


//obtenemos los botones e inputs del DOM
const like = document.getElementById("btn-favorito");
const inputsEstrellas = document.querySelectorAll('.estrellas-voto input');
const promedio = document.getElementById('calificacion');
const contadorLikes = document.getElementById('contador-likes');
const formComentario = document.querySelector('.form-post');
const listaComentarios = document.querySelector('.lista-comentarios');
const infoComentario = document.getElementById('info-comentario');


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

async function agregarComentario() {
    formComentario.addEventListener("submit", async event => {
        event.preventDefault();

        const textarea = formComentario.querySelector("textarea");
        const textoComentario = textarea.value.trim();
        if (!textoComentario) return;

        try {
            const peticion = await fetch(urlComentario, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: idLibroPelicula,
                    type: tipo,
                    texto: textoComentario
                })
            });

            if (!peticion.ok) throw new Error("Error al enviar valores al PHP " + peticion.status);

            const datos = await peticion.json();
            textarea.value = '';

           //traemos el div panel-comunidad
           let panelComunidad = document.querySelector(".panel-comunidad");

            // Buscamos o creamos el div comunidad dentro del panel
            let comunidad = panelComunidad.querySelector('.comunidad');
            if (!comunidad) {
                comunidad = document.createElement('div');
                comunidad.classList.add('comunidad');
                comunidad.innerHTML = `
                    <h3>Comunidad</h3>
                    <div class="lista-comentarios"></div>
                `;
                panelComunidad.appendChild(comunidad);
            }

            //  Insertamos el comentario
            const lista = comunidad.querySelector('.lista-comentarios');
            lista.insertAdjacentHTML("afterbegin", renderComentario(datos.comentario));

        } catch (error) {
            console.error("Error en el fetch al agregar comentario: ", error);
        }
    });
}

function renderComentario(c) {

    const botones = c.esPropio
        ? `
            <div class="acciones-comentario">
                <button class="btn-editar" data-id="${c.id_comentario}">Editar</button>
                <button class="btn-eliminar" data-id="${c.id_comentario}">Eliminar</button>
            </div>`
        : '';

    return `
        <div class="comentario-item" data-id="${c.id_comentario}">
            <img src="${urlFotoUsuario}${c.foto}" class="img-perfil-mini">
            <div class="comentario-cuerpo">
                <strong>${c.username}</strong>
                <small>${c.pais}</small>
                <p class="texto-comentario">${c.texto}</p>
                <small class="tiempo-relativo" data-fecha="${c.fecha}">${c.fecha}</small>
                ${botones}
            </div>
        </div>
    `;
}

document.addEventListener('click', async e => {

    if (e.target.classList.contains('btn-eliminar')) {

        const idComentario = e.target.dataset.id;

        const res = await fetch(urlEliminarComentario, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: idLibroPelicula,
                type: tipo,
                id_comentario: idComentario
            })
        });

        const data = await res.json();

        if (data.status === 'success') {
            document.querySelector(`.comentario-item[data-id="${idComentario}"]`).remove();
        }
    }
});

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


document.addEventListener('click', async (e) => {

    // EDITAR
    if (e.target.classList.contains('btn-editar')) {
        const comentario = e.target.closest('.comentario-item');
        if (!comentario) return;

        const texto = comentario.querySelector('.texto-comentario');
        if (!texto) return;

        texto.dataset.original = texto.textContent;

        texto.innerHTML = `
            <textarea class="edit-text">${texto.dataset.original}</textarea>
            <button class="btn-guardar">Guardar</button>
            <button class="btn-cancelar">Cancelar</button>
        `;
    }

    // CANCELAR
    if (e.target.classList.contains('btn-cancelar')) {
        const comentario = e.target.closest('.comentario-item');
        const texto = comentario.querySelector('.texto-comentario');

        // Restaurar texto original
        texto.textContent = texto.dataset.original;
    }

    // GUARDAR
    if (e.target.classList.contains('btn-guardar')) {
        const comentario = e.target.closest('.comentario-item');
        const textarea = comentario.querySelector('.edit-text');
        const nuevoTexto = textarea.value.trim();

        if (!nuevoTexto) return;

        try {
            const res = await fetch(`/${url}/index.php?ctl=guardarComentario`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: idLibroPelicula,
                    type: tipo,
                    id_comentario: comentario.dataset.id,
                    texto: nuevoTexto
                })
            });

            const data = await res.json();

            if (data.status === 'success') {
                const texto = comentario.querySelector('.texto-comentario');
                texto.textContent = nuevoTexto; // Actualizamos el DOM
            } else {
                alert(data.mensaje || "No se pudo guardar el comentario");
            }
        } catch (error) {
            console.error("Error al guardar comentario:", error);
        }
    }

    // ELIMINAR
    if (e.target.classList.contains('btn-eliminar')) {
        const comentario = e.target.closest('.comentario-item');
        if (!comentario) return;

        const idComentario = comentario.dataset.id;
        if (!idComentario) return;

        try {
            const res = await fetch(urlEliminarComentario, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: idLibroPelicula,
                    type: tipo,
                    id_comentario: idComentario
                })
            });

            const data = await res.json();

            if (data.status === 'success') {
                comentario.remove();

                // Si no queda ningún comentario, borramos la comunidad
                const comunidad = document.querySelector('.comunidad');
                if (comunidad) {
                    const lista = comunidad.querySelectorAll('.comentario-item');
                    if (lista.length === 0) {
                        comunidad.remove();
                    }
                }

                // Si se borró la última comunidad, mostramos mensaje de "Sé el primero en comentar"
                const panelComentarios = document.querySelector('.panel-comentarios');
                if (panelComentarios && !document.querySelector('.comunidad')) {
                    const msjVacio = document.createElement('p');
                    msjVacio.classList.add('msj-vacio');
                    msjVacio.textContent = "Sé el primero en comentar";
                    panelComentarios.appendChild(msjVacio);
                }
            } else {
                console.error(data.mensaje || "No se pudo eliminar el comentario");
            }
        } catch (error) {
            console.error("Error al eliminar comentario:", error);
        }
    }
});

async function agregarALista() {
    const form = document.querySelector(".añadir-lista form");
    if (!form) return;

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch(`/${url}/index.php?ctl=anadir`, {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Añadido a la lista",
                    text: data.mensaje,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.mensaje || "No se pudo añadir"
                });
            }

        } catch (error) {
            console.error("Error al añadir a la lista:", error);
            Swal.fire({
                icon: "error",
                title: "Error inesperado",
                text: "No se pudo procesar la solicitud"
            });
        }
    });
}

function formatearTiempoRelativo() {
    const elementosFecha = document.querySelectorAll('.tiempo-relativo');
    
    elementosFecha.forEach(el => {
        const fechaComentario = new Date(el.dataset.fecha);
        const ahora = new Date();
        const diferenciaSegundos = Math.floor((ahora - fechaComentario) / 1000);

        let texto = "";

        if (diferenciaSegundos < 60) {
            texto = "hace un momento";
        } else if (diferenciaSegundos < 3600) {
            const min = Math.floor(diferenciaSegundos / 60);
            texto = `hace ${min} min`;
        } else if (diferenciaSegundos < 86400) {
            const horas = Math.floor(diferenciaSegundos / 3600);
            texto = `hace ${horas} ${horas === 1 ? 'hora' : 'horas'}`;
        } else {
            const dias = Math.floor(diferenciaSegundos / 86400);
            texto = `hace ${dias} ${dias === 1 ? 'día' : 'días'}`;
        }

        el.textContent = texto;
    });
}

window.onload = function (){
    enviarLike();
    agregarValoracion();
    agregarComentario();
    agregarALista();
    formatearTiempoRelativo();
}
