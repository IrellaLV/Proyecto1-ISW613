<?php
session_start();
require '../includes/functions.php';

$error = null; // Variable para el mensaje de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    
    // Intenta iniciar sesión
    if (login($correo, $contrasena)) {
        // Redirige según el rol
        if ($_SESSION['rol'] == 'Administrador') {
            header('Location: dashboard.php');
        } elseif ($_SESSION['rol'] == 'Amigo') {
            header('Location: dashboard_amigo.php');
        }
        exit();
    } else {
        $error = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
}

// Mensaje de éxito al cerrar sesión
if (isset($_GET['logged_out'])) {
    echo '<div class="alert alert-success" role="alert">Has cerrado sesión exitosamente.</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="d-flex flex-column min-vh-100 justify-content-center bg-light">
    <div class="container">
        <header class="bg-success text-white text-center py-3">
            <h1>Iniciar Sesión</h1>
        </header>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST" class="text-center p-4 border rounded bg-white shadow-sm">
            <div class="form-group">
                <input type="email" name="correo" class="form-control" placeholder="Correo" required>
            </div>
            <div class="form-group">
                <input type="password" name="contrasena" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Iniciar Sesión</button>
            <a href="register.php" class="btn btn-success btn-block">Registrar Amigo</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
