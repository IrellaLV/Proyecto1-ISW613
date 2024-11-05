<?php
include '../includes/functions.php';
//session_start();
check_amigo();

// Procesar la compra si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arbol_id'])) {
    $arbol_id = $_POST['arbol_id'];
    $amigo_id = $_SESSION['usuario_id'];

    // Obtener detalles del árbol
    $arbol = get_tree_by_id($arbol_id);
    if ($arbol && $arbol['estado'] === 'Disponible') {
        // Realizar la compra
        purchase_tree($amigo_id, $arbol_id);
        update_tree_status($arbol_id, 'Vendido', $amigo_id);
        echo "<div class='alert alert-success text-center'>Compra realizada con éxito.</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Este árbol ya no está disponible.</div>";
    }
}

// Obtener árboles en estado 'Disponible'
$arboles = get_available_trees();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Árboles Disponibles</title>
    <!-- Incluir Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php include '../includes/userHeader.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Árboles Disponibles para Compra</h1>

        <?php if (!empty($arboles)): ?>
            <div class="row">
                <?php foreach ($arboles as $arbol): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <!-- Mostrar la foto del árbol -->
                            <?php if (!empty($arbol['foto'])): ?>
                                <img src="../img/<?= htmlspecialchars($arbol['foto']) ?>" class="card-img-top" alt="Foto de <?= htmlspecialchars($arbol['nombre_comercial']) ?>">
                            <?php else: ?>
                                <img src="../images/default_tree.jpg" class="card-img-top" alt="Imagen no disponible">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($arbol['nombre_comercial']) ?></h5>
                                <p class="card-text">Ubicación: <?= htmlspecialchars($arbol['ubicacion_geografica']) ?></p>
                                <p class="card-text">Precio: <span class="font-weight-bold">$<?= htmlspecialchars($arbol['precio']) ?></span></p>
                                
                                <!-- Formulario para realizar la compra -->
                                <form method="POST" class="mt-2">
                                    <input type="hidden" name="arbol_id" value="<?= htmlspecialchars($arbol['id']) ?>">
                                    <button type="submit" class="btn btn-primary btn-block">Comprar este Árbol</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No hay árboles disponibles en este momento.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>