<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Recuperar contraseña</title>
  <link rel="stylesheet" href="web/css/forms.css">
  <link rel="stylesheet" href="web/css/styleFuentes.css">
</head>
<body>

<?php include_once __DIR__ . '/header.php'; ?>

<div class="form-page">
  <div class="form-container">
    <h1>Recuperar contraseña</h1>
    <p>Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

    <form method="POST" action="index.php?ctl=recupero">
      <div>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required />
      </div>

      <button type="submit">Enviar enlace</button>
    </form>

    <div class="footer">
      ¿Recordaste tu contraseña? <a href="index.php?ctl=login">Inicia sesión</a>
    </div>
  </div>
</div>

<script src="./web/js/validacionRecupero.js"></script>
<?php include_once __DIR__ . '/footer.php'; ?>

</body>
</html>
