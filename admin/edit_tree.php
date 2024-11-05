<?php
// Incluir las funciones necesarias
include_once '../includes/functions.php'; // Cambiar a include_once

// Verificar si se pasó el ID del árbol
if (isset($_GET['arbol_id'])) {
    $arbol_id = $_GET['arbol_id'];
    // Obtener los detalles del árbol a editar
    $arbol = get_tree_by_id($arbol_id);

    // Verificar que el árbol existe
    if (!$arbol) {
        echo "Árbol no encontrado.";
        exit;
    }

    // Procesar el formulario al enviar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tamano = $_POST['tamano'];
        $especie_id = $_POST['especie_id'];
        $ubicacion = $_POST['ubicacion'];
        $estado = $_POST['estado'];
    
        // Solo actualiza si hay cambios en los datos
        $arbol_actual = get_tree_by_id($arbol_id);
        
        if ($arbol_actual['tamano_actual'] != $tamano || 
            $arbol_actual['especie_id'] != $especie_id || 
            $arbol_actual['ubicacion_geografica'] != $ubicacion || 
            $arbol_actual['estado'] != $estado) {
    
            updateArbol($arbol_id, $tamano, $especie_id, $ubicacion, $estado);
            addArbolUpdate($arbol_id, $tamano, $estado); // Agrega la actualización
        }
        
        // Redirigir a la página de gestión
        header("Location: manage_amigos.php?amigo_id=" . $arbol_actual['amigo_id']);
        exit;
    }
}    

// Obtener las especies disponibles para el combobox
$especies = get_all_species(); // Asegúrate de tener esta función en functions.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Árbol</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2 class="mt-4">Editar Árbol</h2>
    <form method="POST">
        <div class="form-group">
            <label for="tamano">Tamaño</label>
            <input type="text" name="tamano" id="tamano" class="form-control" value="<?= htmlspecialchars($arbol['tamano_actual'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="especie_id">Especie</label>
            <select name="especie_id" id="especie_id" class="form-control" required>
                <?php foreach ($especies as $especie): ?>
                    <option value="<?= $especie['id'] ?>" <?= $arbol['especie_id'] == $especie['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($especie['nombre_comercial']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="ubicacion">Ubicación Geográfica</label>
            <input type="text" name="ubicacion" id="ubicacion" class="form-control" value="<?= htmlspecialchars($arbol['ubicacion_geografica'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="Disponible" <?= $arbol['estado'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                <option value="Vendido" <?= $arbol['estado'] == 'Vendido' ? 'selected' : '' ?>>Vendido</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>