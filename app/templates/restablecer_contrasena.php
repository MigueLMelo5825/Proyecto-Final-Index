<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Restablecer contraseña</title>
  <link rel="stylesheet" href="web/css/forms.css">
  <link rel="stylesheet" href="web/css/styleFuentes.css">
</head>
<body>

<?php include_once __DIR__ . '/header.php'; ?>

<div class="form-page">
  <div class="form-container">
    <h1>Nueva contraseña</h1>

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
      <a href="index.php?ctl=login">Volver al login</a>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>

</body>
</html>
