<?php
include '../includes/functions.php';
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Amigo') {
    header("Location: login.php");
    exit();
}

// Obtén los árboles comprados por el amigo
$amigo_id = $_SESSION['usuario_id'];
$mis_arboles = get_trees_by_amigo_id($amigo_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Amigo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e6f5e6;
        }
        .header-links {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .header-links a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        .profile-card {
            background-color: white;
            border: 1px solid #4caf50;
            padding: 20px;
            margin-top: 20px;
        }
        .tree-card {
            margin: 10px 0;
            border: 1px solid #4caf50;
            border-radius: 5px;
            padding: 10px;
            background-color: #fff;
        }
    </style>
</head>
<body>
<?php include '../includes/userHeader.php'; ?>
<div class="container">
    <div class="profile-card text-center">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($_SESSION['correo']); ?></p>
    </div>

    <div class="container mt-4">
    <h3>Mis Árboles Comprados</h3>
    <table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Especie</th>
            <th>Tamaño</th>
            <th>Estado</th>
            <th>Foto</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mis_arboles as $arbol): ?>
        <tr>
            <td><?php echo htmlspecialchars($arbol['id']); ?></td>
            <td><?php echo htmlspecialchars($arbol['especie_nombre']); ?></td>
            <td><?php echo htmlspecialchars($arbol['tamano_actual']); ?></td>
            <td><?php echo htmlspecialchars($arbol['estado']); ?></td>
            <td>
                <img src="../img/<?php echo htmlspecialchars($arbol['foto']); ?>" 
                     alt="Foto del árbol" 
                     style="width: 50px; height: auto;" 
                     onerror="this.onerror=null; this.src='../img/default_image.jpg';">
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>
<?php include '../includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>