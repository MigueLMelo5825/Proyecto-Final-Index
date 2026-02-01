


// document.getElementById("loginForm").addEventListener("submit", function(e) {
//     e.preventDefault();

//     const usuario = document.getElementById("usuario").value.trim();
//     const password = document.getElementById("password").value.trim();

//     const errorUsuario = document.getElementById("errorUsuario");
//     const errorPassword = document.getElementById("errorPassword");


//     errorUsuario.textContent = "";
//     errorPassword.textContent = "";

//     let valido = true;

//     // Validación usuario/email
//     if (usuario === "") {
//         errorUsuario.textContent = "El usuario o email no puede estar vacío";
//         valido = false;
//     } else if (usuario.length < 3) {
//         errorUsuario.textContent = "Debe tener al menos 3 caracteres";
//         valido = false;
//     }

//     // Validación contraseña
//     if (password === "") {
//         errorPassword.textContent = "La contraseña no puede estar vacía";
//         valido = false;
//     } else if (password.length < 6) {
//         errorPassword.textContent = "Debe tener al menos 6 caracteres";
//         valido = false;
//     }


//     if (valido) {
//         Swal.fire({
//             title: "Correcto",
//             text: "Datos validados correctamente",
//             icon: "success",
//             confirmButtonText: "Continuar"
//         }).then(() => {
            
//             document.getElementById("loginForm").submit();
//         });
//     }
// });



document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const usuario = document.getElementById("usuario").value.trim();
    const password = document.getElementById("password").value.trim();

    let valido = true;
    let mensajeError = "";

    // Validar si es email o nombre
    const esEmailValido = esEmail(usuario);
    const esNombreValido = esNombre(usuario);

    if (!esEmailValido && !esNombreValido) {
        valido = false;
        mensajeError = "Debes ingresar un email válido o un nombre de al menos 3 caracteres sin espacios";
    }

    // Validar contraseña
    if (valido && password.length < 6) {
        valido = false;
        mensajeError = "La contraseña debe tener al menos 6 caracteres";
    }

   
  
});

function esEmail(valor) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(valor);
}

function esNombre(valor) {
    return valor.length >= 3 && !valor.includes(" ");
}
