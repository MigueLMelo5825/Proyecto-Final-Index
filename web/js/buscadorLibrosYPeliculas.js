//creo la funcion para cargar los archivos al buscador

//url del php para obtener los libros
const urlPhp = "../../app/templates/buscador_Libros.php";

//obtengo lasvariables globables del archivo html
const inputLibro = document.getElementById("inputLibro");
const divLibrosEncontrados = document.getElementById("libroOPeliculaEncontrada");


//variables de control
let contenido = "";
const cache = {};

function mostrarLibro(){

    inputLibro.addEventListener("keyup", event => {

        event.preventDefault();

        //obtenemos el valor de libro
        var textoLibro = inputLibro.value;

        if(textoLibro.length > 0 ){

            divLibrosEncontrados.style.display = "block";
            cargarLibro(textoLibro);
        
        }else{

            divLibrosEncontrados.innerHTML = "";
            divLibrosEncontrados.style.display = "none"
        }
    })
}

//------------------------------FUNCION QUE TRAE LA INFORMACION DEL PHP-----------------------

async function cargarLibro(textoLibro){

    try{

        if(cache[textoLibro]){

            cache[textoLibro].crearLista();
            funcionesLista();
            return;
        }

        const peticionPHP = await fetch(urlPhp, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                libro: textoLibro
            })
        });

        if(!peticionPHP.ok){

            throw new Error("Error al cargar el archivo php " + peticionPHP.statusText);
        }

        const libros = await peticionPHP.json();

        console.log(libros);

        divLibrosEncontrados.innerHTML = "";

        //el php devuelve un array el cual se puede validar directamente
        if (libros.length === 0) {
            divLibrosEncontrados.innerHTML = "<p>No se encontraron libros</p>";
            return;
        }

        //como el php me devuelve un array indexado lo que debo de hacer es recorrer ese array e insertar sus valores dentro de arrayLibros
        const arrayLibros = [];

        libros.forEach(libro =>{
            arrayLibros.push({
                id: libro.id,
                nombre: libro.titulo,
                autores: libro.autores,
                categoria: libro.categoria,
                imagen_url: libro.imagen_url
            })
        });


        console.log(arrayLibros)
        //guardamos la palabra buscada en el cache junto el array encontrado
        cache[inputLibro.value] = arrayLibros;

        //ordenamos el array para mostrarlo por pantalla
        arrayLibros.sort((a, b) => a.nombre.localeCompare(b.nombre));

        //creamos la lista con la funcion prototype
        arrayLibros.crearLista();

        //cargamos todas las funciones que tiene la lista
        funcionesLista();
    
    }catch(error){

        console.error("Error en el fetch valide el codigo" + error);
    }
}

Array.prototype.crearLista = function (){

    divLibrosEncontrados.innerHTML = "";

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
            const divTexto = document.createElement("div");
            const pTitulo = document.createElement("p");
            const pAutores = document.createElement("p");
            const pCategoria = document.createElement("p");
            
            //asignamos los valores para mostrar
            id = m.id;
            img.src = m.imagen_url ? m.imagen_url.replace("http://", "https://") : "fallback.jpg";

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
        divLibrosEncontrados.style.display = "block";
        divLibrosEncontrados.appendChild(divLibro);
        divLibro.appendChild(ul);

        funcionesLista();

}

function funcionesLista(){

    //creamos eventos en los li para poder ser seleccionados dentro de la lista y dar un foco
    const liLibros = divLibrosEncontrados.querySelectorAll("li");
        
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

            //obtenemos el elemento enfocado
            const elementoActual = document.activeElement;

            switch(event.key){
                case "ArrowDown":
                    event.preventDefault();
                    const siguiente = elementoActual.nextElementSibling;
                    if(siguiente) siguiente.focus();
                break;
                case "ArrowUp":
                    event.preventDefault();
                    const anterior = elementoActual.previousElementSibling;
                    if(anterior) anterior.focus();
                break;
                case "Enter":
                    event.preventDefault();

                    if(inputLibro.value !== ""){
                        //lo limpiamos
                        inputLibro.value = "";
                            
                        //agregamos el valor
                        inputLibro.value = li.textContent;
                    }else{

                        //agregamos el valor directamente
                        inputLibro.value = li.textContent;
                        divLibrosEncontrados.style.display = "none";
                        //mensaje.style.display = "none";
                    }
                break;
                default:
                    return;
            }
        })

        li.addEventListener("click", event => {
            event.preventDefault();
                
            if(inputLibro.value !== ""){
                //lo limpiamos
                inputLibro.value = "";
                            
                //agregamos el valor
                inputLibro.value = li.textContent;
            }else{

                //agregamos el valor directamente
                inputLibro.value = li.textContent;
                divLibrosEncontrados.style.display = "none";
                //mensaje.style.display = "none";
            }
        })

        // Si el clic no es dentro del buscador, cerramos la lista
        document.addEventListener("click", (event) => {
            if (!document.getElementById("infoLibro").contains(event.target)) {
            divLibrosEncontrados.style.display = "none";
            }
        });
    })
}

window.onload = function (){
    mostrarLibro();
}
