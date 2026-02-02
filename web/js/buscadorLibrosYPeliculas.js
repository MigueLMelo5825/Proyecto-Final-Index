//creo la funcion para cargar los archivos al buscador

//url del php para obtener los libros
const urlPhpLibros = "../../app/templates/buscador_Libros.php";
const urlPhpPeliculas = "../../app/templates/buscador_Peliculas.php";

//obtengo lasvariables globables del archivo html
const inputLibroPelicula = document.getElementById("inputLibro");
const divEncontrados = document.getElementById("libroOPeliculaEncontrada");


//variables de control
let contenido = "";
const cache = {};

function mostrarLibroPelicula(){

    inputLibroPelicula.addEventListener("keyup", event => {

        event.preventDefault();

        //obtenemos el valor de libro
        let textoLibroPelicula = inputLibroPelicula.value;

        if(textoLibroPelicula.length > 0 ){

            divEncontrados.style.display = "block";
            cargarLibroPelicula(textoLibroPelicula);
        
        }else{

            divEncontrados.innerHTML = "";
            divEncontrados.style.display = "none"
        }
    })
}

//------------------------------FUNCION QUE TRAE LA INFORMACION DEL PHP-----------------------

async function cargarLibroPelicula(textoLibroPelicula){

    try{

        //validamos que el texto buscado no este entre el cache si no esta ejecutamos todo el proceso
        if(cache[textoLibroPelicula]){

            cache[textoLibroPelicula].crearLista();
            funcionesLista();
            return;
        }

        //codigo para obtener los libros y guardalos en el array

        const peticionPHPLibros = await fetch(urlPhpLibros, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                libro: textoLibroPelicula
            })
        });

        if(!peticionPHPLibros.ok){

            throw new Error("Error al cargar el archivo php " + peticionPHPLibros.statusText);
        }

        const libros = await peticionPHPLibros.json();

        //console.log(libros);

        divEncontrados.innerHTML = "";

        //el php devuelve un array el cual se puede validar directamente
        if (libros.length === 0) {
            divEncontrados.innerHTML = "<p>No se encontraron libros</p>";
            return;
        }

        //como el php me devuelve un array indexado lo que debo de hacer es recorrer ese array e insertar sus valores dentro de arrayLibrosPeliculas
        const arrayLibrosPeliculas = [];

        libros.forEach(libro =>{
            arrayLibrosPeliculas.push({
                id: libro.id,
                nombre: libro.titulo,
                autores: libro.autores,
                categoria: libro.categoria,
                imagen_url: libro.imagen_url
            })
        });

        //CODIGO PARA OBTENER LAS PELICULAS ATRAVEZ DEL SERVIDOR PHP
        
        const peticionPhpPeliculas = await fetch(urlPhpPeliculas, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                pelicula: textoLibroPelicula
            })
        });

        if(!peticionPhpPeliculas.ok){
            throw new Error("Error al cargar las peliculas revisar archivo php" + peticionPhpPeliculas.statusText);
        }

        const peliculas = await peticionPhpPeliculas.json();

        console.log(peliculas);
        
        
        //guardamos la palabra buscada en el cache junto el array encontrado
        cache[inputLibroPelicula.value] = arrayLibrosPeliculas;

        //ordenamos el array para mostrarlo por pantalla
        arrayLibrosPeliculas.sort((a, b) => a.nombre.localeCompare(b.nombre));

        //creamos la lista con la funcion prototype
        arrayLibrosPeliculas.crearLista();

        //cargamos todas las funciones que tiene la lista
        funcionesLista();
    
    }catch(error){

        console.error("Error en el fetch valide el codigo" + error);
    }
}

//creop una funcion personaliza usando Array.prototype para crear la lista de libros y mostrarla por pantalla---------------------//

Array.prototype.crearLista = function (){

    divEncontrados.innerHTML = "";

    //creamos el div que estara dentro del div de libros y peliculas encontradas, esto con el fin de que cada libro encontrado sea un div con su informacion
    const divLibro = document.createElement("div");
    divLibro.id = "infoLibro";

    //agregamos este div al 

    //estos seran los elementos dentro del div info
    const ul = document.createElement("ul");



        this.forEach(m =>{

            const li = document.createElement("li");
            li.style.display = "flex"

            //creamos los valores que guardaran los resultados y los mostrara en pantalla
            const img = document.createElement("img");
            li.dataset.id = m.id;
            li.dataset.type = "libro";
            const divTexto = document.createElement("div");
            const pTitulo = document.createElement("p");
            const pAutores = document.createElement("p");
            const pCategoria = document.createElement("p");
            
            //asignamos los valores para mostrar
            img.src = m.imagen_url ? m.imagen_url.replace("http://", "https://") : "../../web/img/fallback.png";

            pTitulo.innerHTML = `<strong>${m.nombre}</strong>`;
            pAutores.textContent = "Autor: " + (m.autores || "Desconocido");
            pCategoria.textContent = "Categoría: " + (m.categoria || "N/A");


            // agregamos los valores al div texto
            divTexto.appendChild(pTitulo);
            divTexto.appendChild(pAutores);
            divTexto.appendChild(pCategoria);

            //agregamos los valores al li
            li.appendChild(img);
            li.appendChild(divTexto);
            
            //agregamos al ul para mostrar en el div de informacion
            ul.appendChild(li);
            
        });

        //activamos el div y mostramos las sugerencias
        divEncontrados.style.display = "block";
        divEncontrados.appendChild(divLibro);
        divLibro.appendChild(ul);

        funcionesLista();

}

function funcionesLista(){

    //creamos eventos en los li para poder ser seleccionados dentro de la lista y dar un foco
    const liLibros = divEncontrados.querySelectorAll("li");
        
    liLibros.forEach(li => {

        //aca hacemos que cada li sea enfocable
        li.setAttribute("tabindex", "0");

        //agregamos el evento de foco individual a cada uno
        li.addEventListener("focus", event => {

            //cambiamos el color de cada li cuando tenga el foco
            event.target.style.backgroundColor = "#d8d8d8";
            //li.classList.add("seleccionado");
        })

        //volvemos a colocar transparente al perder el foco
        li.addEventListener("blur", event => {
            
            //usando el event target para realizar los cambios
            event.target.style.backgroundColor = "transparent";
            //li.classList.remove("seleccionado");
        })

        li.addEventListener("keydown", event => {

            switch(event.key){
                case "ArrowDown":
                    event.preventDefault();
                    
                    li.nextElementSibling?.focus();
                break;
                case "ArrowUp":
                    event.preventDefault();
                    
                    li.previousElementSibling?.focus();
                break;
                case "Enter":
                    event.preventDefault();

                    seleccionarLibro(li);
                break;
            }
        })

        //agregamos evento al dar click sobre el objeto libro
        li.addEventListener("click", event => {

            event.preventDefault();

            const liLibro = event.target.closest("li[data-id]");
            if(!liLibro) return;

            // Validar que no haga nada con texto vacio
            if (inputLibroPelicula.value.trim() === "") {
                return;
            }

            seleccionarLibro(liLibro);
        })
    })
}

function seleccionarLibro(li){

    //colocamos el texto del libro seleccionado en el inputLibroPelicula
    inputLibroPelicula.value = li.textContent.trim();

    //ocultamos el buscado al tener un libro ya seleccionado y borramos el input 
    inputLibroPelicula.textContent = "";
    divEncontrados.style.display = "none";

    //obtenemos el id del libro desde el atributo oculto data-id como el tipo si es libro/pelicula
    const idLibro = li.dataset.id;
    const typeLibro = li.dataset.type;

    //si no existe el id cancelamos el proceso de redireccion al igual que el tipo
    if(!idLibro && !typeLibro) return;

    //construimos la url de redireccion
    const urlPhp = `../../app/templates/ficha_Libro_Y_Peliculas.php?=id${encodeURIComponent(idLibro)}?type=${encodeURIComponent(typeLibro)}`;

    //redireccionamos a la ficha de libro o pelicula
    window.location.href = urlPhp;

}

//creamos funcion para que al momento de dar click fuera del buscado se cierre la ventana
function cerrarBuscador(event){
    
    if(document.getElementById("infoLibro").contains(event.target)){
        divEncontrados.style.display = "none";
    }
}

window.onload = function (){
    mostrarLibro();
}
