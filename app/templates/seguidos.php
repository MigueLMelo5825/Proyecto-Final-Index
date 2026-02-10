<h2>Siguiendo</h2>

<ul class="list-group">
<?php foreach ($seguidos as $s): ?>
    <li class="list-group-item d-flex align-items-center">
        <img src="<?= $s['foto'] ?>" width="40" height="40" class="rounded-circle me-2">
        <a href="index.php?ctl=perfil&id=<?= $s['id'] ?>">
            <?= htmlspecialchars($s['nombre']) ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>
