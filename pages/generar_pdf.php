<?php
require('fpdf/fpdf.php');
class PDF extends FPDF {
    // Cabecera de página
    function Header() {
        // Margen izquierdo a 10mm
        $this->SetLeftMargin(10);
        // Margen derecho a 10mm
        $this->SetRightMargin(10);
        
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Título
        $this->Cell(0,10,'LISTA DE ALUMNOS',0,1,'C');
        // Salto de línea
        $this->Ln(10);
        // Cabeceras de la tabla
        $this->SetFont('Arial','B',11);
        $this->SetFillColor(242,242,242);
        
        // Calculamos el ancho disponible (Legal = 355.6mm de ancho)
        // Restamos 20mm de márgenes totales (10mm cada lado)
        $anchoDisponible = 335.6;
        
        // Distribuimos el espacio
        $this->Cell($anchoDisponible * 0.25,7,'ALUMNO',1,0,'C',true);
        $this->Cell($anchoDisponible * 0.15,7,'FECHA NAC.',1,0,'C',true);
        $this->Cell($anchoDisponible * 0.40,7,utf8_decode('TELÉFONOS'),1,0,'C',true);
        $this->Cell($anchoDisponible * 0.20,7,'HORARIO',1,1,'C',true);
    }
    
    // Pie de página
    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

try {
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

    // Crear nuevo PDF en formato oficio horizontal
    $pdf = new PDF('L','mm','Legal');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);

    // Ancho disponible para las celdas
    $anchoDisponible = 335.6; // Ancho total - márgenes

    foreach($datos as $row) {
        // Calcular alto necesario para los contactos
        $contactos = explode("\n", $row['contactos']);
        $altoCelda = max(7, count($contactos) * 7); // 7mm por línea

        // Dibujar celdas con el mismo alto usando porcentajes del ancho disponible
        $pdf->Cell($anchoDisponible * 0.25, $altoCelda, utf8_decode($row['alumno']), 1, 0, 'L');
        $pdf->Cell($anchoDisponible * 0.15, $altoCelda, date('d/m/Y', strtotime($row['fecha_nacimiento'])), 1, 0, 'C');
        
        // Guardar posición actual
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        
        // Celda para contactos
        $pdf->MultiCell($anchoDisponible * 0.40, 7, utf8_decode($row['contactos']), 1, 'L');
        
        // Volver a la posición correcta
        $pdf->SetXY($x + ($anchoDisponible * 0.40), $y);
        
        // Horario
        $horario = $row['hora_inicio'] . ' a ' . $row['hora_termino'];
        $pdf->Cell($anchoDisponible * 0.20, $altoCelda, $horario, 1, 1, 'C');
    }

    // Salida del PDF
    $pdf->Output('I', 'lista_alumnos.pdf');
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>