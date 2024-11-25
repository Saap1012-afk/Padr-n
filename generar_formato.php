<?php
require_once('tcpdf/tcpdf/tcpdf.php');

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "mascotas";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se pasó un ID válido por la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obtener la información básica de la mascota
    $sqlMascota = "SELECT m.nombre_mascota, m.raza, m.numero_arete, m.especie, dm.edad, dm.unidad_tiempo, dm.fecha_ultima_vacunacion, dm.genero, dm.esterilizada, dm.chip_tatuaje, dm.señas_particulares, d.nombre_dueño, m.usuario_id
                   FROM mascotas m
                   INNER JOIN detalles_mascotas dm ON m.id = dm.mascota_id
                   INNER JOIN dueños d ON m.dueño_id = d.id
                   WHERE m.id = $id";
    $resultMascota = $conn->query($sqlMascota);

    if ($resultMascota->num_rows > 0) {
        $rowMascota = $resultMascota->fetch_assoc();

        // Consulta para obtener las vacunas suministradas a la mascota
        $sqlVacunas = "SELECT vacuna
                       FROM vacunas
                       WHERE mascota_id = $id";
        $resultVacunas = $conn->query($sqlVacunas);

        // Crear instancia de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Establecer información del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nombre de la institución');
        $pdf->SetTitle('Cartilla de Vacunación de Mascota');
        $pdf->SetSubject('Cartilla de Vacunación');

        // Agregar una página
        $pdf->AddPage();

        // Logo
        $pdf->Image('img/canifeli.jpg', 150, 11, 45, 0, '', '', 'T', false, 300, '', false, false, 0);

        // Agregar usuario_id en la parte superior derecha
        $pdf->SetXY(0, 0); // Ajustar posición X e Y para el usuario_id
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0); // Negro
        $pdf->Cell(0, 10, 'Cédula del veterinario: ' . $rowMascota['usuario_id'], 0, 1, 'R');

        // Nombre de la institución a la izquierda
        $pdf->SetXY(10, 18); // Ajustar posición X e Y para el nombre de la institución
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(241, 90, 37); // Azul oscuro
        $pdf->Cell(0, 10, 'Padrón Municipal', 0, 1, 'L');

        // Título "Cartilla de Vacunación"
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(0, 0, 0); // Negro
        $pdf->Ln(10); // Espacio
        $pdf->Cell(0, 10, 'Cartilla de Vacunación', 0, 1, 'C');

        // Subtítulo "Cuidado y Bienestar Animal"
        $pdf->SetFont('helvetica', 'I', 16);
        $pdf->SetTextColor(128, 128, 128); // Gris
        $pdf->Cell(0, 10, 'Cuidado y Bienestar Animal', 0, 1, 'C');

        // Foto de la mascota en la esquina superior izquierda
        $imageHeight = 40; // Altura de la imagen
        $imageWidth = 40; // Anchura de la imagen
        $pdf->Ln(10); // Espacio antes de la sección de la imagen

        // Consulta para obtener la ruta de la foto de la mascota
        $sqlFoto = "SELECT ruta
                    FROM fotos
                    WHERE mascota_id = $id";
        $resultFoto = $conn->query($sqlFoto);

        if ($resultFoto->num_rows > 0) {
            $rowFoto = $resultFoto->fetch_assoc();
            $foto = $rowFoto['ruta'];

            if (file_exists($foto)) {
                $pdf->Image($foto, 85, 60, $imageWidth, $imageHeight, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
            } else {
                $pdf->SetTextColor(255, 0, 0); // Rojo
                $pdf->Cell(0, 10, 'Foto de la mascota no encontrada.', 0, 1, 'L');
            }
        } else {
            $pdf->SetTextColor(255, 0, 0); // Rojo
            $pdf->Cell(0, 10, 'No hay foto disponible para esta mascota.', 0, 1, 'L');
        }

        // Datos de la mascota
        $pdf->SetY(110); // Ajusta la posición de inicio de los datos
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(241, 90, 37); // Azul oscuro
        $pdf->Cell(0, 10, 'Datos de la Mascota', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(0, 0, 0); // Negro

        // Usar celdas con bordes para mejor visualización
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Nombre: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $rowMascota["nombre_mascota"], 1, 1);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Raza: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $rowMascota["raza"], 1, 1);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Número de Arete: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $rowMascota["numero_arete"], 1, 1);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Edad: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);

        // Concatenar edad y unidad de tiempo
        $edadCompleta = $rowMascota["edad"] . ' ' . $rowMascota["unidad_tiempo"];
        $pdf->Cell(0, 10, $edadCompleta, 1, 1);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Vacunación o Consulta: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $rowMascota["fecha_ultima_vacunacion"], 1, 1);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Esterilizada: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $rowMascota["esterilizada"], 1, 1);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Chip/Tatuaje: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $rowMascota["chip_tatuaje"], 1, 1);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(50, 10, 'Dueño: ', 1, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $rowMascota["nombre_dueño"], 1, 1);

        // Vacunas Aplicadas
        $pdf->Ln(10); // Espacio antes de la tabla de vacunas
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(241, 90, 37); // Azul oscuro
        $pdf->Cell(0, 10, 'Historial clínico', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(0, 0, 0); // Negro

        // Crear tabla para vacunas
        $pdf->SetFillColor(230, 230, 230); // Color de fondo de las celdas
        $pdf->Cell(0, 10, 'Descripción:', 1, 1, 'C', true);

        // Configura el ancho de la columna en función del ancho de la página menos los márgenes
$anchoColumna = $pdf->GetPageWidth() - 20; // Ajusta según sea necesario

// Iterar sobre las vacunas y agregar filas a la tabla
while ($rowVacunas = $resultVacunas->fetch_assoc()) {
    // Establece el alto de la celda y el ancho
    $pdf->SetX(10); // Ajusta la posición X según sea necesario
    $pdf->MultiCell($anchoColumna, 10, $rowVacunas["vacuna"], 1, 'L');
    
    // Mueve la posición Y a la siguiente línea
    $pdf->Ln();
}

        // Agregar información adicional
        $pdf->Ln(2); // Espacio antes de la sección de firmas
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(241, 90, 37); // Azul oscuro
        $pdf->Cell(0, 10, 'Firma del Veterinario: ', 0, 1, 'C');
        $pdf->Cell(0, 10, '_____________________________', 0, 1, 'C');

        // Salida del PDF
        $pdf->Output('Cartilla_Vacunacion_Mascota.pdf', 'I');
    } else {
        echo "No se encontró ninguna mascota con el ID proporcionado.";
    }
} else {
    echo "ID de mascota no válido.";
}

$conn->close();
?>
