<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Restablecer contraseña</title>
  <link rel="stylesheet" href="../../web/css/styleRecupero.css">
</head>
<body>
<div class="container">
  <h1>Nueva contraseña</h1>

  <!-- <?php if (!empty($errores['token'])): ?>
    <p class="error"><?= htmlspecialchars($errores['token']) ?></p>
  <?php endif; ?> -->

  <form method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <div>
      <label>Nueva contraseña</label>
      <input type="password" name="password" required>
      <?php if (!empty($errores['password'])) echo "<p class='error'>{$errores['password']}</p>"; ?>
    </div>

    <div>
      <label>Repite la contraseña</label>
      <input type="password" name="password2" required>
      <?php if (!empty($errores['password2'])) echo "<p class='error'>{$errores['password2']}</p>"; ?>
    </div>

    <button type="submit">Actualizar contraseña</button>
  </form>

  <div class="footer">
    <a href="login.php">Volver al login</a>
  </div>
</div>
</body>
</html>