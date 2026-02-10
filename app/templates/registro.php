<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro</title>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="./web/css/styleRegistro.css">
</head>

<body>

  <div class="container">
    <h1>Crear cuenta</h1>

    <?php 
      // Variables seguras para repoblar campos
      $username = $params['username'] ?? '';
      $name     = $params['name'] ?? '';
      $email    = $params['email'] ?? '';
      $errores  = $params['errores'] ?? [];
    ?>

    <form id="form" method="POST" action="index.php?ctl=registro">

      <!-- USERNAME -->
      <div>
        <label for="username">Nombre de usuario</label>
        <input 
          type="text" 
          id="username" 
          name="username" 
          placeholder="ej: maria.lopez"
          value="<?= htmlspecialchars($username) ?>"
          required 
          minlength="3" 
          maxlength="50"
          pattern="[A-Za-z0-9._-]+"
        />
        <?php if (!empty($errores['username'])): ?>
          <p class="error"><?= $errores['username'] ?></p>
        <?php endif; ?>
        <small>Solo letras, números, puntos, guiones y guion bajo.</small>
      </div>

      <!-- NOMBRE REAL -->
      <div>
        <label for="name">Nombre</label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          placeholder="Introduce tu nombre" 
          value="<?= htmlspecialchars($name) ?>"
          required 
        />
        <?php if (!empty($errores['name'])): ?>
          <p class="error"><?= $errores['name'] ?></p>
        <?php endif; ?>
      </div>

      <!-- EMAIL -->
      <div>
        <label for="email">Correo electrónico</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          placeholder="Introduce tu correo" 
          value="<?= htmlspecialchars($email) ?>"
          required 
        />
        <?php if (!empty($errores['email'])): ?>
          <p class="error"><?= $errores['email'] ?></p>
        <?php endif; ?>
      </div>

      <!-- PAÍS -->
      <div>
        <label for="pais_id">País</label>
        <select name="pais_id" id="pais_id">
          <?php foreach ($paises as $p): ?>
              <option 
                value="<?= $p['id_pais'] ?>"
                <?= (isset($_POST['pais_id']) && $_POST['pais_id'] == $p['id_pais']) ? 'selected' : '' ?>
              >
                <?= $p['nombre'] ?>
              </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- CONTRASEÑA -->
      <div>
        <label for="password">Contraseña</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          placeholder="Introduce tu contraseña" 
          required 
        />
        <?php if (!empty($errores['password'])): ?>
          <p class="error"><?= $errores['password'] ?></p>
        <?php endif; ?>
      </div>

      <!-- REPETIR CONTRASEÑA -->
      <div>
        <label for="password2">Repetir contraseña</label>
        <input 
          type="password" 
          id="password2" 
          name="password2" 
          placeholder="Vuelve a escribirla" 
          required 
        />
        <?php if (!empty($errores['password2'])): ?>
          <p class="error"><?= $errores['password2'] ?></p>
        <?php endif; ?>
      </div>

      <input type="hidden" name="rol" value="usuario" />

      <button type="submit">Registrarme</button>
    </form>

    <div class="footer">
      ¿Ya tienes cuenta? <a href="index.php?ctl=login">Inicia sesión</a>
    </div>
  </div>

  <script src="./web/js/validacionRegistro.js"></script>

</body>
</html>
