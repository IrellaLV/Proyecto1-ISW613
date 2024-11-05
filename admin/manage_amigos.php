<?php
// manage_amigos.php
include '../includes/functions.php'; // Asume que aquí están las funciones de base de datos

// Consultar todos los amigos registrados
$amigos = getAllAmigos();

if (isset($_GET['amigo_id'])): 
    $amigo_id = $_GET['amigo_id'];
    $arboles = getArbolesByAmigo($amigo_id);
endif;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Amigos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h1 class="mt-4">Administrar Amigos</h1>

    <!-- Listado de Amigos -->
    <h2 class="mt-4">Lista de Amigos</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Ver Árboles</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($amigo = $amigos->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($amigo['nombre']) ?></td>
                    <td><?= htmlspecialchars($amigo['apellidos']) ?></td>
                    <td>
                        <a href="manage_amigos.php?amigo_id=<?= $amigo['id'] ?>&amigo_nombre=<?= urlencode($amigo['nombre']) ?>" class="btn btn-info">Ver Árboles</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Detalles y edición de árboles del amigo -->
    <?php if (isset($_GET['amigo_id'])): ?>
        <h2 class="mt-4">Árboles de <?= htmlspecialchars($_GET['amigo_nombre'] ?? 'Amigo') ?></h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Especie</th>
                    <th>Tamaño</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($arbol = $arboles->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($arbol['especie']) ?></td> <!-- Asegúrate de que especie_id está presente -->
            <td><?= htmlspecialchars($arbol['tamano_actual'] ?? 'N/A') ?></td> <!-- Cambié tamano por tamano_actual -->
            <td><?= htmlspecialchars($arbol['ubicacion_geografica']) ?></td>
            <td><?= htmlspecialchars($arbol['estado']) ?></td>
            <td><a href="edit_tree.php?arbol_id=<?= $arbol['id'] ?>" class="btn btn-primary">Editar</a></td>
        </tr>
    <?php endwhile; ?>
</tbody>
        </table>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>