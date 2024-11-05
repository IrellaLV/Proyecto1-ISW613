<?php
require '../vendor/autoload.php'; // Asegúrate de que esta ruta sea correcta

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function getConnection() {
    $connection = mysqli_connect('localhost', 'root', '', 'proyecto1');
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $connection;
}

function verificarArbolesDesactualizados() {
    $conn = getConnection();
    
    // Consulta SQL para obtener árboles desactualizados
    $query = "
     SELECT 
        a.id AS arbol_id,
        e.nombre_comercial,
        e.nombre_cientifico,
        MAX(up.fecha) AS fecha_ultima_actualizacion
    FROM 
        arboles a
    JOIN 
        actualizaciones up ON a.id = up.arbol_id
    JOIN 
        especies e ON a.especie_id = e.id
    WHERE 
        up.fecha = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
    GROUP BY 
        a.id, e.nombre_comercial, e.nombre_cientifico
    HAVING 
        MAX(up.fecha) IS NOT NULL
    LIMIT 0, 25;
"; 
    // Mostrar consulta para depuración
    echo "Consulta SQL: $query\n"; // Línea de depuración
    $result = mysqli_query($conn, $query);
    
    // Verificar errores en la consulta
    if (!$result) {
        echo "Error en la consulta: " . mysqli_error($conn);
        return [];
    }
    
    $arbolesDesactualizados = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $arbolesDesactualizados[] = $row;
    }
    
    return $arbolesDesactualizados;
}

function enviarCorreo($cuerpoCorreo) {
    $mail = new PHPMailer(true); // Crear una instancia de PHPMailer
    try {
        // Configuraciones del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Especificar el servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'ireleon1503@gmail.com'; // Tu correo
        $mail->Password = 'fwva yvzo pyby rimi'; // Tu contraseña
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Destinatarios
        $mail->setFrom('ireleon1503@gmail.com', 'Irella, Administrador Amigos de un Millón de Árboles');
        $mail->addAddress('ireleon1503@gmail.com', 'Administradora Irella');

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Árboles desactualizados';
        $mail->Body    = nl2br($cuerpoCorreo); // Convertir nuevas líneas en <br>
        $mail->AltBody = $cuerpoCorreo; // Contenido alternativo para clientes de correo que no soportan HTML

        $mail->send();
        echo 'El mensaje ha sido enviado';
    } catch (Exception $e) {
        echo "El mensaje no se pudo enviar. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Ejecución de la función
$arbolesDesactualizados = verificarArbolesDesactualizados();
if (!empty($arbolesDesactualizados)) {
    // Generar el cuerpo del correo con los árboles desactualizados
    $cuerpoCorreo = "Los siguientes árboles no han sido actualizados desde hace 1 mes:\n";
    foreach ($arbolesDesactualizados as $arbol) {
        $cuerpoCorreo .= "- " . $arbol['nombre_comercial'] . " (" . $arbol['nombre_cientifico'] . ")\n";
    }
    echo $cuerpoCorreo; // Mostrar el contenido del correo para depuración

    // Enviar correo
    enviarCorreo($cuerpoCorreo);
} else {
    echo "No hay árboles desactualizados.\n";
}