<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro</title>

<link rel="stylesheet" href="../../web/css/styleRegistro.css">
<script src="/Proyecto/web/js/validacionRegistro.js"></script>
</head>

<body>
  <div class="container">
    <h1>Crear cuenta</h1>
<br><br>

    <form>
      <div>
        <label for="name">Nombre </label>
        <input type="text" id="name" placeholder="nombre" required />
      </div>

      <div>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" placeholder="correo" required />
      </div>

      <div>
        <label for="password">Contraseña</label>
        <input type="password" id="password" placeholder="contraseña" required />
      </div>

      <div>
        <label for="password2">Repetir contraseña</label>
        <input type="password" id="password2" placeholder="Vuelve a escribirla" required />
      </div>

      <button type="submit">Registrarme</button>
    </form>

    <div class="footer">
      ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </div>
  </div>
</body>
</html>
