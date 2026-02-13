<?php require __DIR__ . "/header.php"; ?>
<link rel="stylesheet" href="web/css/forms.css">
<link rel="stylesheet" href="web/css/styleFuentes.css">

<div class="form-page">

  <div class="form-container">

    <!-- Card central -->
    <div class="form-card">
      <h2>Iniciar sesión</h2>

      <form id="loginForm" method="POST" action="index.php?ctl=login">

        <div class="form-group">
          <label for="email">Email
            <input type="text" id="email" name="email" placeholder="Email" required>
            <p id="errorUsuario" class="error"></p>
          </label>
        </div>

        <div class="form-group">
          <label for="password">Contraseña
            <input type="password" id="password" name="password">
            <p id="errorPassword" class="error"></p>
          </label>
        </div>

        <button type="submit">Entrar</button>
      </form>

      <div class="footer">
        <p>¿No tienes cuenta? <a href="index.php?ctl=registro">Regístrate</a></p>
        <p>¿Olvidaste la contraseña? <a href="index.php?ctl=recupero">Recuperar la contraseña</a></p>
      </div>

    </div>

  </div>

</div>

<?php if (!empty($_SESSION['swal'])): ?>
<script> 
    Swal.fire({
        icon: <?= json_encode($_SESSION['swal']['icon']) ?>,
        title: <?= json_encode($_SESSION['swal']['title']) ?>,
        text: '<?= $_SESSION['swal']['text'] ?>',
        confirmButtonText: 'Aceptar'
    });
</script>
<?php unset($_SESSION['swal']); ?>
<?php endif; ?>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
