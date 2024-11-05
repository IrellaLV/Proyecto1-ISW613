<?php
include '../includes/functions.php';
check_admin(); // Asegúrate de que el usuario tenga acceso a esta página

// Procesar las operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Lógica para agregar una nueva especie
        $nombre_comercial = $_POST['nombre_comercial'];
        $nombre_cientifico = $_POST['nombre_cientifico'];
        insert_species($nombre_comercial, $nombre_cientifico);
    } elseif (isset($_POST['edit'])) {
        // Lógica para editar una especie existente
        $id = $_POST['id'];
        $nombre_comercial = $_POST['nombre_comercial'];
        $nombre_cientifico = $_POST['nombre_cientifico'];
        update_species($id, $nombre_comercial, $nombre_cientifico);
    } elseif (isset($_POST['delete'])) {
        // Lógica para eliminar una especie
        $id = $_POST['id'];
        delete_species($id);
    }
}

// Obtener todas las especies
$species = get_all_species();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Especies de Árboles</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?> 
    <div class="container mt-5">
        <h1 class="text-center mb-4">Administrar Especies de Árboles</h1>

        <!-- Formulario para agregar nueva especie -->
        <form action="" method="POST" class="mb-4">
            <div class="form-row">
                <div class="col-md-5 mb-3">
                    <label for="nombre_comercial">Nombre Comercial</label>
                    <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial" required>
                </div>
                <div class="col-md-5 mb-3">
                    <label for="nombre_cientifico">Nombre Científico</label>
                    <input type="text" class="form-control" id="nombre_cientifico" name="nombre_cientifico" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label>&nbsp;</label>
                    <button type="submit" name="add" class="btn btn-success btn-block">Agregar Especie</button>
                </div>
            </div>
        </form>

        <!-- Tabla de especies -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Comercial</th>
                    <th>Nombre Científico</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($species as $specie): ?>
                    <tr>
                        <td><?= $specie['id'] ?></td>
                        <td><?= $specie['nombre_comercial'] ?></td>
                        <td><?= $specie['nombre_cientifico'] ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?= $specie['id'] ?>">Editar</button>
                            <!-- Modal para editar especie -->
                            <div class="modal fade" id="editModal<?= $specie['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Editar Especie</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST">
                                                <input type="hidden" name="id" value="<?= $specie['id'] ?>">
                                                <div class="form-group">
                                                    <label for="nombre_comercial">Nombre Comercial</label>
                                                    <input type="text" class="form-control" name="nombre_comercial" value="<?= $specie['nombre_comercial'] ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nombre_cientifico">Nombre Científico</label>
                                                    <input type="text" class="form-control" name="nombre_cientifico" value="<?= $specie['nombre_cientifico'] ?>" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    <button type="submit" name="edit" class="btn btn-primary">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Botón para eliminar -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $specie['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>