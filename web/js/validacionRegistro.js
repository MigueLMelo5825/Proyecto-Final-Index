document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const pass1 = document.getElementById("password").value.trim();
    const pass2 = document.getElementById("password2").value.trim();

    if (name === "") {
      Swal.fire("Nombre requerido", "Escribe tu nombre.", "warning");
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      Swal.fire("Correo inválido", "Introduce un correo válido.", "warning");
      return;
    }

    if (pass1.length < 6) {
      Swal.fire("Contraseña muy corta", "Debe tener al menos 6 caracteres.", "warning");
      return;
    }

    if (pass1 !== pass2) {
      Swal.fire("No coinciden", "Las contraseñas deben ser iguales.", "warning");
      return;
    }

    Swal.fire({
      icon: "success",
      title: "Registro correcto",
      text: "Tu cuenta ha sido creada."
    }).then(() => form.submit());
  });
});
