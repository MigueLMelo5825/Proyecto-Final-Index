<?php include_once __DIR__ . '/header.php'; ?>
<link rel="stylesheet" href="web/css/forms.css">
<link rel="stylesheet" href="web/css/styleFuentes.css">

<div class="list-page">
  <div class="list-container">
    <h2>Explorar usuarios</h2>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="index.php" class="usuarios-form">
      <input type="hidden" name="ctl" value="buscarUsuarios">

      <input type="text"
             name="q"
             placeholder="Buscar por usuario..."
             value="<?= htmlspecialchars($termino ?? '') ?>">

      <button type="submit">Buscar</button>
    </form>

    <!-- Lista de usuarios -->
    <?php if (!empty($usuarios)): ?>
      <ul class="usuarios-list list-group">

        <?php foreach ($usuarios as $u): ?>

          <!-- Ocultar tu propia cuenta -->
          <?php if ($u['id'] == $_SESSION['id_usuario']) continue; ?>

          <li class="usuario-item list-group-item">

            <!-- Foto -->
            <img src="<?= htmlspecialchars($u['foto']) ?>"
                 width="40" height="40"
                 alt="@<?= htmlspecialchars($u['username']) ?>">

            <!-- Solo username -->
            <div class="usuario-info">
              <a href="index.php?ctl=perfil&id=<?= $u['id'] ?>">
                @<?= htmlspecialchars($u['username']) ?>
              </a>
            </div>

          </li>

        <?php endforeach; ?>

      </ul>

    <?php elseif (!empty($termino)): ?>
      <p class="no-results">No se encontraron usuarios con ese nombre.</p>
    <?php endif; ?>

  </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>
