<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Recuperar contraseña</title>

<link rel="stylesheet" href="../../web/css/styleRecupero.css">


</head>

<body>
  <div class="container">
    <h1>Recuperar contraseña</h1>
    <br>
    <p>Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

<form method="POST" action="?controller=recuperacion&action=enviarEmail">
      <div>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required />
      </div>

      <button type="submit">Enviar enlace</button>
    </form>

    <div class="footer">
      ¿Recordaste tu contraseña? <a href="login.php">Inicia sesión</a>
    </div>
  </div>
  

  
  <script src="../../web/js/validacionRecupero.js"></script>
</body>
</html>
