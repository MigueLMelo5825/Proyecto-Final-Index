//creo la funcion para cargar los archivos al buscador

//url del php para obtener los libros
const urlPhp = "../../app/templates/busca_Libro.php";

//obtengo lasvariables globables del archivo html
const inputLibro = document.getElementById("buscador");
const divLibrosEncontrados = document.getElementById("librosEncontrados");


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
                nombre:libro
            })
        });

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

    const ul = document.createElement("ul");

        this.forEach(m =>{

            const li = document.createElement("li");
            li.textContent = m.nombre;

            ul.appendChild(li);
        });

        //activamos el div y mostramos las sugerencias
        divLibrosEncontrados.style.display = "block";
        divLibrosEncontrados.appendChild(ul);

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
    })
}

window.onload = function (){
    mostrarLibro();
}
