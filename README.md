Guia para descarga de repositorio

crear una carpeta vacia local en la que guardaran el proyecto.

abrir esa carpeta con el programa el cual usaran para el desarrollo del programa en mi caso VSCode.

descargar las extensiones git de VSCode

crontol + ñ abre la terminal de VSCode y alli ejecutamos los siguientes comandos:

git init para iniciar un repositorio en nuestra carpeta

git remote add origin https://github.com/MigueLMelo5825/Proyecto-Final-Index.git

git branch -M main

git pull origin main

con git pull descargan todo lo que el proyecto tiene actualmente

listo ya tenemos enlazado el proyecto remoto, ahora para subir nuestros archivos, modificaciones y mas, deben de seguir los siguientes pasos:

abrir nuevamente la terminal y ejecutar         git add .

luego                                                                  git commit -m “especificando los cambios o lo que han creado como comentario“

git push -u origin main

el git push -u origin main solo se debe de usar la primera vez que vayamos a subir un archivo o modificacion al repositorio remoto, despues debemos de ejecutar solo git push

SUPER IMPORTANTE ANTES DE DESARROLLAR CUALQUIER COSA DEL PROYECTO DEBEN DE HACER UN git pull
