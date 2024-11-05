<?php
session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio de sesión u otra página deseada
header('Location: /Proyecto1/admin/login.php');
exit();

