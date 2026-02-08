<?php
// ============================================================
// ver_lista.php
// ============================================================

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);

// Valores por defecto por si faltan datos
if (!isset($lista)) {
    $lista = [
        'nombre' => 'Lista desconocida',
        'descripcion' => '',
        'id' => 0
    ];
}

if (!isset($items)) {
    $items = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($lista['nombre']) ?> – Lista</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="web/css/styleperfil.css">
</head>
<body>

<?php include_once __DIR__ . '/../templates/header.php'; ?>

<main class="container my-5">

    <h2><?= htmlspecialchars($lista['nombre']) ?></h2>
    <p><?= htmlspecialchars($lista['descripcion']) ?></p>

    <hr>

    <h3>Elementos de la lista</h3>

    <?php if (empty($items)): ?>
        <p>No hay elementos en esta lista.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($items as $item): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($item['titulo'] ?? 'Sin título') ?></strong><br>
                    <?= htmlspecialchars($item['descripcion'] ?? '') ?><br>
                    <small>Creado en: <?= htmlspecialchars($item['creado_en'] ?? 'N/A') ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
