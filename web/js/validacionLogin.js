

document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const usuario = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    let valido = true;
    let mensajeError = "";

    
    const esEmailValido = esEmail(usuario);
   

    if (!esEmailValido) {
        valido = false;
        mensajeError = "Debes ingresar un email válido";
    }

    // Validar contraseña
    if (valido && password.length < 6) {
        valido = false;
        mensajeError = "La contraseña debe tener al menos 6 caracteres";
    }

    document.getElementById("loginForm").submit();
});

function esEmail(valor) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(valor);
}

function esNombre(valor) {
    return valor.length >= 3 && !valor.includes(" ");
}
