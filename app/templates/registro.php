<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro</title>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../web/css/styleRegistro.css">
</head>

<body>

  <div class="container">
    <h1>Crear cuenta</h1>

    <form id="form" method="POST" action="../../index.php?ctl=registro">

      <div>
        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" placeholder="nombre" required />
      </div>

      <div>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="correo" required />
      </div>

      <div>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="contraseña" required />
      </div>

      <div>
        <label for="password2">Repetir contraseña</label>
        <input type="password" id="password2" name="password2" placeholder="Vuelve a escribirla" required />
      </div>

      <button type="submit">Registrarme</button>
    </form>

    <div class="footer">
      ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </div>
  </div>

  <script src="../../web/js/validacionRegistro.js"></script>

</body>
</html>
