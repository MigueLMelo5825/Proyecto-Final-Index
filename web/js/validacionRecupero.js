
document.querySelector('form').addEventListener('submit', function(e) {
    const emailInput = document.getElementById('email');
    const emailValue = emailInput.value.trim();
    
    // Regex básica para validar formato de email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailValue === "") {
        e.preventDefault();
        alert("Por favor, introduce tu correo electrónico.");
        emailInput.focus();
    } else if (!emailPattern.test(emailValue)) {
        e.preventDefault();
        alert("Por favor, introduce un formato de correo válido.");
        emailInput.focus();
    }
});

