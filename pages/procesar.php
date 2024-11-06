<?php
// Configuración de la conexión a la base de datos
$servidor = "localhost";
$usuario = "skyper";
$password = "ctpalm2113";
$bd = "escuela";

try {
    // Crear conexión usando PDO
    $pdo = new PDO("mysql:host=$servidor;dbname=$bd;charset=utf8", $usuario, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // 1. Insertar en la tabla persona
    $stmt = $pdo->prepare("INSERT INTO persona (nombre, fecha_nacimiento) VALUES (?, ?)");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['fecha_nacimiento']
    ]);
    
    // Obtener el ID de la persona recién insertada
    $id_persona = $pdo->lastInsertId();
    
    // 2. Insertar en la tabla horario
    $stmt = $pdo->prepare("INSERT INTO horario (id_persona, hora_inicio, hora_termino) VALUES (?, ?, ?)");
    $stmt->execute([
        $id_persona,
        $_POST['hora_inicio'],
        $_POST['hora_termino']
    ]);
    
    // 3. Insertar en la tabla contacto según el tipo seleccionado
    switch($_POST['telefono_tipo']) {
        case 'mama':
            insertarContacto($pdo, $id_persona, 'mamá', $_POST['telefono_mama'], $_POST['nombre_mama']);
            break;
        case 'papa':
            insertarContacto($pdo, $id_persona, 'papá', $_POST['telefono_papa'], $_POST['nombre_papa']);
            break;
        case 'ambos':
            insertarContacto($pdo, $id_persona, 'mamá', $_POST['telefono_mama'], $_POST['nombre_mama']);
            insertarContacto($pdo, $id_persona, 'papá', $_POST['telefono_papa'], $_POST['nombre_papa']);
            break;
    }
    
    // Confirmar la transacción
    $pdo->commit();
    
    // Responder con éxito
    echo json_encode(['success' => true, 'message' => 'Registro completado con éxito']);
    
} catch(PDOException $e) {
    // Si hay error, hacer rollback
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Responder con error
    echo json_encode(['success' => false, 'message' => 'Error al procesar el registro: ' . $e->getMessage()]);
}

// Función auxiliar para insertar contactos
function insertarContacto($pdo, $id_persona, $tipo, $telefono, $nombre) {
    $stmt = $pdo->prepare("INSERT INTO contacto (id_persona, tipo, telefono, nombre_contacto) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $id_persona,
        $tipo,
        $telefono,
        $nombre
    ]);
}
?>