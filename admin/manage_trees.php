<?php
include '../includes/functions.php';
check_admin();

// Eliminar árbol
if (isset($_GET['delete'])) {
    delete_tree($_GET['delete']);
    header('Location: manage_trees.php');
    exit;
}

// Obtener todos los árboles
$arboles = get_all_trees();

// Obtener todas las especies 
$especies = get_all_species();

// Verificar si estamos en modo de edición
$arbol = null;
if (isset($_GET['edit'])) {
    $arbol = get_tree_by_id($_GET['edit']);
}

// Manejo de la acción de agregar o editar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        update_tree($_POST['id'], $_POST['especie_id'], $_POST['ubicacion'], $_POST['estado'], $_POST['precio'], $_FILES['foto']);
    } else {
        insert_tree($_POST['especie_id'], $_POST['ubicacion'], $_POST['estado'], $_POST['precio'], $_FILES['foto']);
    }
    header('Location: manage_trees.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Árboles</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container mt-5">
        <h1>Administrar Árboles</h1>

        <!-- Formulario para agregar/editar árbol -->
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="form-group">
                <label for="especie_id">Especie</label>
                <select class="form-control" id="especie_id" name="especie_id" required>
                    <?php foreach ($especies as $especie): ?>
                        <option value="<?= $especie['id'] ?>" <?= isset($arbol) && $arbol['especie_id'] == $especie['id'] ? 'selected' : '' ?>>
                            <?= $especie['nombre_comercial'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ubicacion">Ubicación Geográfica</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?= isset($arbol) ? $arbol['ubicacion_geografica'] : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="Disponible" <?= isset($arbol) && $arbol['estado'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="Vendido" <?= isset($arbol) && $arbol['estado'] == 'Vendido' ? 'selected' : '' ?>>Vendido</option>
                </select>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" value="<?= isset($arbol) ? $arbol['precio'] : '' ?>" min="1" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto del Árbol (sube desde tu dispositivo)</label>
                <input type="file" name="foto" accept="image/*" <?= !isset($arbol) ? 'required' : '' ?>>
            </div>
            <input type="hidden" name="id" value="<?= isset($arbol) ? $arbol['id'] : '' ?>">
            <button type="submit" class="btn btn-success"><?= isset($arbol) ? 'Actualizar' : 'Agregar' ?> Árbol</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Precio</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arboles as $arbol_item): ?>
                    <tr>
                        <td>
                            <?php 
                            foreach ($especies as $especie) {
                                if ($especie['id'] == $arbol_item['especie_id']) {
                                    echo $especie['nombre_comercial'];
                                    break;
                                }
                            }
                            ?>
                        </td>
                        <td><?= $arbol_item['ubicacion_geografica'] ?></td>
                        <td><?= $arbol_item['estado'] ?></td>
                        <td><?= $arbol_item['precio'] ?></td>
                        <td><img src="../img/<?= $arbol_item['foto'] ?>" width="100" alt="Foto"></td>
                        <td>
                            <a href="manage_trees.php?edit=<?= $arbol_item['id'] ?>" class="btn btn-warning">Editar</a>
                            <a href="manage_trees.php?delete=<?= $arbol_item['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este árbol?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>