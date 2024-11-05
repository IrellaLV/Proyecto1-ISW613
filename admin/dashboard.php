<?php
include '../includes/functions.php';
// check_admin();

// Obtener estadísticas
$stats = get_admin_stats();

// Obtener fotos de los árboles
$arboles = get_all_trees_with_photos(); // Función que obtendrá los árboles con fotos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
    <!-- Incluir Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir los estilos personalizados -->
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Incluir el header -->
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-5">
        <h1 class="text-center mb-5">Dashboard Administrador</h1>

        <!-- Carrusel de Imágenes -->
        <div id="carouselArboles" class="carousel slide mb-5" data-ride="carousel" data-interval="4000">
            <div class="carousel-inner">
                <?php 
                $active = true; // Variable para activar solo la primera imagen
                foreach ($arboles as $arbol): ?>
                    <div class="carousel-item <?= $active ? 'active' : '' ?>">
                        <img src="../img/<?= $arbol['foto'] ?>" class="d-block w-100" alt="Foto de árbol">
                        <div class="carousel-caption d-none d-md-block">
                            <h5><?= $arbol['nombre_comercial'] ?></h5>
                            <p>Ubicación: <?= $arbol['ubicacion_geografica'] ?></p>
                        </div>
                    </div>
                    <?php $active = false; // Desactivar después de la primera imagen ?>
                <?php endforeach; ?>
            </div>
            <a class="carousel-control-prev" href="#carouselArboles" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#carouselArboles" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Siguiente</span>
            </a>
        </div>

        <div class="row">
            <!-- Tarjeta para Amigos Registrados -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Amigos Registrados</h5>
                        <p class="card-text display-4"><?= $stats['amigos'] ?></p>
                    </div>
                </div>
            </div>
            <!-- Tarjeta para Árboles Disponibles -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Árboles Disponibles</h5>
                        <p class="card-text display-4"><?= $stats['disponibles'] ?></p>
                    </div>
                </div>
            </div>
            <!-- Tarjeta para Árboles Vendidos -->
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Árboles Vendidos</h5>
                        <p class="card-text display-4"><?= $stats['vendidos'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir el footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>