<?php include_once __DIR__ . '/header.php'; ?>
<link rel="stylesheet" href="web/css/forms.css">
<link rel="stylesheet" href="web/css/styleFuentes.css">

<div class="list-page">
  <div class="list-container">
    <h2>Seguidores</h2>

    <ul class="list-group">
      <?php foreach ($seguidores as $s): ?>
        <li class="list-group-item">
          <img src="<?= $s['foto'] ?>" width="40" height="40" alt="Foto de <?= htmlspecialchars($s['nombre']) ?>">
          <a href="index.php?ctl=perfil&id=<?= $s['id'] ?>">
            <?= htmlspecialchars($s['nombre']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>
