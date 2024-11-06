<?php
// Conexión a la base de datos
$pdo = new PDO("mysql:host=localhost;dbname=escuela;charset=utf8", "skyper", "ctpalm2113");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Consulta para obtener los datos
$query = "
    SELECT 
        p.nombre AS alumno,
        p.fecha_nacimiento,
        h.hora_inicio,
        h.hora_termino,
        GROUP_CONCAT(
            CONCAT(c.tipo, ': ', c.nombre_contacto, ' - ', c.telefono)
            SEPARATOR '\n'
        ) AS contactos
    FROM persona p
    LEFT JOIN horario h ON p.id_persona = h.id_persona
    LEFT JOIN contacto c ON p.id_persona = c.id_persona
    GROUP BY p.id_persona
";

$datos = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de Alumnos</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-generar {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px 0;
        }
        .btn-generar:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Datos de Alumnos</h2>
    
    <form action="generar_pdf.php" method="post" target="_blank">
        <button type="submit" class="btn-generar">Generar PDF</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ALUMNO</th>
                <th>FECHA DE NACIMIENTO</th>
                <th>TELÉFONO DE MAMÁ Y PAPÁ</th>
                <th>HORARIO DE SERVICIO</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['alumno']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['fecha_nacimiento'])); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['contactos'])); ?></td>
                <td><?php 
                    echo htmlspecialchars($row['hora_inicio']) . ' a ' . 
                         htmlspecialchars($row['hora_termino']); 
                ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>