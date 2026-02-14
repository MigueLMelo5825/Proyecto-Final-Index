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
    <link rel="stylesheet" href="web/css/styleFuentes.css">
</head>
<body>

<?php include_once __DIR__ . '/../templates/header.php'; ?>

<main class="container my-5">

    <h2><?= htmlspecialchars($lista['nombre']) ?></h2>
    <p><?= htmlspecialchars($lista['descripcion']) ?></p>

    <hr>

    <h3 class="mb-4">Elementos de la lista</h3>

    <?php if (empty($items)): ?>
        <p>No hay elementos en esta lista.</p>
    <?php else: ?>
    <div class="list-group">
        <?php foreach ($items as $item): ?>
        <div class="list-group-item">
            <div class="d-flex gap-3 align-items-start">

            <!-- Imagen -->
            <div class="flex-shrink-0">
                <img
                src="<?= htmlspecialchars($item['imagen'] ?? '') ?>"
                alt="<?= htmlspecialchars($item['titulo'] ?? 'Sin título') ?>"
                class="rounded-3 shadow-sm"
                style="width: 90px; height: 135px; object-fit: cover;"
                onerror="this.style.display='none';"
                >
            </div>

            <!-- Texto -->
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                <h6 class="mb-1 fw-bold">
                    <?= htmlspecialchars($item['titulo'] ?? 'Sin título') ?>
                </h6>
                <small class="text-muted ms-3 text-nowrap">
                    <?= htmlspecialchars($item['añadido_en'] ?? '') ?>
                </small>
                </div>

                <p class="mb-0 text-muted descripcion-clamp">
                <?= htmlspecialchars($item['descripcion'] ?? '') ?>
                </p>
            </div>

            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>


</main>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
