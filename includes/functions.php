<?php
function getConnection() {
    $connection = mysqli_connect('localhost', 'root', '', 'proyecto1');
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $connection;
}

function login($correo, $contrasena) {
    $conn = getConnection();
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $correo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($result);

    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        
        // Redirect based on role
        if ($_SESSION['rol'] === 'Administrador') {
            header('Location: ../admin/dashboard.php');
        } else if ($_SESSION['rol'] === 'Amigo') {
            header('Location: ../amigos/dashboard.php');
        }
        exit();
    } else {
        return false; // Failed login
    }
}

function get_amigo_info($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT nombre, apellidos, telefono, correo, direccion, pais FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc(); // Devuelve los datos del amigo como un arreglo asociativo
    }
    return null; // Retorna null si no se encuentra el usuario
}

function check_admin() {
    session_start();
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'Administrador') {
        header('Location: no_access.php');
        exit();
    }
}

function check_amigo() {
    session_start();
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'Amigo') {
        header('Location: no_access.php');
        exit();
    }
}

// Obtener todos los árboles
function get_all_trees() {
    $conn = getConnection();
    $sql = "SELECT * FROM arboles";
    $result = mysqli_query($conn, $sql);
    
    $arboles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $arboles[] = $row;
    }
    return $arboles;
}

// Obtener un árbol específico por su ID
function get_tree_by_id($arbol_id) {
    $conn = getConnection();
    $sql = "SELECT arboles.*, especies.nombre_comercial AS nombre_especie 
            FROM arboles 
            JOIN especies ON arboles.especie_id = especies.id 
            WHERE arboles.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $arbol_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}
 
function insert_tree($especie_id, $ubicacion, $estado, $precio, $foto) {
    $conn = getConnection();

    // Validar el archivo de imagen (opcional)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($foto['type'], $allowed_types)) {
        die("Formato de imagen no permitido. Solo se aceptan JPEG, PNG, y GIF.");
    }

    // Subir la imagen
    $foto_nombre = time() . '_' . basename($foto['name']);
    $foto_ruta = __DIR__ . '/../img/' . $foto_nombre; // Asegurar ruta correcta con '/../img/'

    if (move_uploaded_file($foto['tmp_name'], $foto_ruta)) {
        echo "Imagen subida exitosamente.";
    } else {
        die("Error al mover la imagen. Verifica permisos y ruta.");
    }

    // Convertir el precio a entero
    $precio = (int) $precio;

    // Preparar la consulta SQL
    $sql = "INSERT INTO arboles (especie_id, ubicacion_geografica, estado, precio, foto) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'issis', $especie_id, $ubicacion, $estado, $precio, $foto_nombre);

    if (mysqli_stmt_execute($stmt)) {
        echo "Árbol insertado correctamente.";
    } else {
        echo "Error al insertar el árbol: " . mysqli_stmt_error($stmt);
    }

    // Cerrar la conexión y liberar recursos
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

function update_tree($arbol_id, $especie_id, $ubicacion, $estado, $precio, $foto = null) {
    $conn = getConnection();

    // Actualizar árbol
    $sql = "UPDATE arboles SET especie_id = ?, ubicacion_geografica = ?, estado = ?, precio = ?";

    if ($foto) {
        // Subir foto
        $foto_nombre = time() . '_' . basename($foto['name']);
        $foto_ruta = '../img/' . $foto_nombre;
        move_uploaded_file($foto['tmp_name'], $foto_ruta);
        $sql .= ", foto = '$foto_nombre'";
    }

    $sql .= " WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'isssi', $especie_id, $ubicacion, $estado, $precio, $arbol_id);
    mysqli_stmt_execute($stmt);
}

// Eliminar un árbol por su ID
function delete_tree($arbol_id) {
    $conn = getConnection();
    $sql = "DELETE FROM arboles WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $arbol_id);
    mysqli_stmt_execute($stmt);
}

function get_admin_stats() {
    $conn = getConnection();

    // Obtener estadísticas
    $query = "SELECT COUNT(*) AS amigos FROM usuarios WHERE rol = 'amigo'";
    $result = $conn->query($query);
    $amigos = $result->fetch_assoc()['amigos'];

    $query = "SELECT COUNT(*) AS disponibles FROM arboles WHERE estado = 'Disponible'";
    $result = $conn->query($query);
    $disponibles = $result->fetch_assoc()['disponibles'];

    $query = "SELECT COUNT(*) AS vendidos FROM arboles WHERE estado = 'Vendido'";
    $result = $conn->query($query);
    $vendidos = $result->fetch_assoc()['vendidos'];

    // Obtener árboles disponibles con fotos
    $query = "SELECT * FROM arboles WHERE estado = 'Disponible'";
    $result = $conn->query($query);
    $arboles = [];
    while ($row = $result->fetch_assoc()) {
        $arboles[] = $row;
    }

    return [
        'amigos' => $amigos,
        'disponibles' => $disponibles,
        'vendidos' => $vendidos,
        'arboles' => $arboles // Agregar los árboles disponibles
    ];
}

// Insertar un nuevo amigo
function insert_amigo($nombre, $apellidos, $telefono, $correo, $direccion, $pais, $contrasena) {
    $conn = getConnection();

    $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT); // Encriptar la contraseña
    $sql = "INSERT INTO usuarios (nombre, apellidos, telefono, correo, direccion, pais, contrasena, rol) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    // Define the role for the new user
    $rol = "Amigo"; 
    
    // Ensure you're binding the correct number of parameters
    mysqli_stmt_bind_param($stmt, 'ssssssss', $nombre, $apellidos, $telefono, $correo, $direccion, $pais, $contrasena_hash, $rol);
    return mysqli_stmt_execute($stmt);
}

// Obtener todas las especies
function get_all_species() {
    $conn = getConnection();
    $sql = "SELECT * FROM especies";
    $result = mysqli_query($conn, $sql);
    
    $especies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $especies[] = $row;
    }
    return $especies;
}

// Obtener una especie específica por su ID
function get_species_by_id($especie_id) {
    $conn = getConnection();
    $sql = "SELECT * FROM especies WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $especie_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Insertar una nueva especie
function insert_species($nombre_comercial, $nombre_cientifico) {
    $conn = getConnection();
    $sql = "INSERT INTO especies (nombre_comercial, nombre_cientifico) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $nombre_comercial, $nombre_cientifico);
    return mysqli_stmt_execute($stmt);
}

// Actualizar una especie existente
function update_species($especie_id, $nombre_comercial, $nombre_cientifico) {
    $conn = getConnection();
    $sql = "UPDATE especies SET nombre_comercial = ?, nombre_cientifico = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssi', $nombre_comercial, $nombre_cientifico, $especie_id);
    return mysqli_stmt_execute($stmt);
}

// Eliminar una especie por su ID
function delete_species($especie_id) {
    $conn = getConnection();
    $sql = "DELETE FROM especies WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $especie_id);
    return mysqli_stmt_execute($stmt);
}

function get_all_trees_with_photos() {
    $conn = getConnection();
    // Consulta para obtener los árboles con fotos y su información de especie
    $sql = "SELECT arboles.*, especies.nombre_comercial, arboles.ubicacion_geografica 
            FROM arboles 
            JOIN especies ON arboles.especie_id = especies.id 
            WHERE arboles.foto IS NOT NULL";
    $result = $conn->query($sql);
    
    // Verificar si hay resultados y devolverlos en un array asociativo
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

function get_available_trees() {
    $conn = getConnection();
    $sql = "SELECT arboles.*, especies.nombre_comercial, arboles.foto 
            FROM arboles 
            JOIN especies ON arboles.especie_id = especies.id 
            WHERE arboles.estado = 'Disponible'";
    $result = $conn->query($sql);

    $available_trees = [];
    while ($row = $result->fetch_assoc()) {
        $available_trees[] = $row;
    }
    return $available_trees;
}

function update_tree_status($arbol_id, $nuevo_estado, $amigo_id) {
    $conn = getConnection();

    // Consulta para insertar un nuevo registro en la tabla `compras`
    $query = "INSERT INTO compras (amigo_id, arbol_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $amigo_id, $arbol_id);

    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }

    return true;
}

function purchase_tree($amigo_id, $arbol_id) {
    $conn = getConnection();
    $conn->begin_transaction();

    try {
        // Check the current status of the tree before updating
        $check_sql = "SELECT estado FROM arboles WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $arbol_id);
        $check_stmt->execute();
        $check_stmt->bind_result($current_estado);
        $check_stmt->fetch();

        // Always free the result set to avoid commands out of sync error
        $check_stmt->free_result(); 

        if ($current_estado === 'Vendido') {
            $check_stmt->close();
            $conn->rollback(); // Rollback if the tree is already sold
            $conn->close();
            return false; // Tree is already sold
        }

        // Update the tree status to "Vendido"
        $update_sql = "UPDATE arboles SET estado = 'Vendido' WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $arbol_id);
        $update_stmt->execute();

        // Insert the purchase record into 'compras' table
        $insert_sql = "INSERT INTO compras (amigo_id, arbol_id, fecha_compra) VALUES (?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $amigo_id, $arbol_id);
        $insert_stmt->execute();

        // Commit the transaction if both operations succeed
        $conn->commit();
        
        // Close statements
        $update_stmt->close();
        $insert_stmt->close();
        $check_stmt->close();
        $conn->close();
        
        return true; // Indicate success
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        
        // Log the error
        error_log("Error during purchase transaction: " . $e->getMessage());

        // Close connection
        $conn->close();
        
        return false; // Indicate failure
    }
}

function getAllAmigos() {
    $conn = getConnection();
    $sql = "SELECT id, nombre, apellidos FROM usuarios WHERE rol = 'Amigo'";
    return mysqli_query($conn, $sql);
}

// Consultar árboles de un amigo específico
function getArbolesByAmigo($amigo_id) {
    $conn = getConnection(); // Conexión a la base de datos
    $sql = "
        SELECT arboles.id, arboles.ubicacion_geografica, arboles.estado, arboles.precio, 
               especies.id AS especie_id, especies.nombre_comercial AS especie, 
               actualizaciones.tamano AS tamano_actual
        FROM arboles
        JOIN especies ON arboles.especie_id = especies.id
        LEFT JOIN compras ON arboles.id = compras.arbol_id
        LEFT JOIN actualizaciones ON arboles.id = actualizaciones.arbol_id
        WHERE compras.amigo_id = ?
        ORDER BY arboles.id
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $amigo_id);
    $stmt->execute();
    return $stmt->get_result();
}


function updateArbol($arbol_id, $tamano, $especie_id, $ubicacion, $estado) {
    $conn = getConnection();
    $sql = "UPDATE arboles SET tamano_actual = ?, especie_id = ?, ubicacion_geografica = ?, estado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $tamano, $especie_id, $ubicacion, $estado, $arbol_id);
    return $stmt->execute();
}

// Registrar una actualización de árbol
function addArbolUpdate($arbol_id, $tamano, $estado) {
    $conn = getConnection();

    // Verificar si ya existe una entrada en la tabla actualizaciones para el árbol
    $sql_check = "SELECT id FROM actualizaciones WHERE arbol_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $arbol_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Actualizar el registro existente en lugar de insertar uno nuevo
        $sql_update = "UPDATE actualizaciones SET fecha = NOW(), tamano = ?, estado = ? WHERE arbol_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $tamano, $estado, $arbol_id);
        return $stmt_update->execute();
    } else {
        // Insertar un nuevo registro si no existe una entrada previa
        $sql_insert = "INSERT INTO actualizaciones (arbol_id, fecha, tamano, estado) VALUES (?, NOW(), ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iss", $arbol_id, $tamano, $estado);
        return $stmt_insert->execute();
    }
}

function get_trees_by_amigo_id($amigo_id) {
    $conn = getConnection(); // Asegúrate de que $mysqli está inicializado correctamente
    $query = "
        SELECT a.id, a.tamano_actual, a.estado, e.nombre_comercial AS especie_nombre, a.foto
        FROM compras c
        JOIN arboles a ON c.arbol_id = a.id
        JOIN especies e ON a.especie_id = e.id
        WHERE c.amigo_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $amigo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC); // Retorna un array asociativo
}