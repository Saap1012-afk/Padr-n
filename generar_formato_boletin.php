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

    // Consulta para obtener la información de la mascota y los boletines
    $sql = "
        SELECT 
            b.foto, b.fecha_perdida, b.lugar_perdida, 
            m.nombre AS nombre_mascota, m.especie, m.raza, m.genero, m.edad, m.unidad_t, m.señas_particulares, 
            d.nombre AS nombre_dueño, d.contacto, d.dir_dueño 
        FROM 
            boletines b 
            JOIN mascotas_boletin m ON b.mascota_id = m.id 
            JOIN dueños_boletin d ON m.dueño_id = d.id 
        WHERE 
            b.id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Crear instancia de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Establecer información del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nombre de la institución');
        $pdf->SetTitle('Boletín de Búsqueda de Mascotas');
        $pdf->SetSubject('Boletín de Búsqueda de Mascotas');

        // Agregar una página
        $pdf->AddPage();

        // Título del boletín
        $pdf->SetFont('times', 'B', 60);
        $pdf->Cell(0, 10, '!SE BUSCA¡', 0, 1, 'C');

        $imagePath = $row["foto"];
        $imageWidth = 160; // Ancho deseado
        $imageHeight = 140; // Altura deseada, 0 para ajustar proporcionalmente

        if (!empty($imagePath) && file_exists($imagePath)) {
            // Obtener dimensiones de la imagen
            list($width, $height) = getimagesize($imagePath);
            if ($imageHeight == 0) {
                // Calcular altura proporcional
                $imageHeight = $height * ($imageWidth / $width);
            }
            // Calcular posición para centrar la imagen
            $x = ($pdf->GetPageWidth() - $imageWidth) / 2;
            $y = $pdf->GetY();
            // Agregar la imagen al PDF
            $pdf->Image($imagePath, $x, $y, $imageWidth, $imageHeight, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            // Actualizar la posición Y para el texto
            $pdf->SetY($y + $imageHeight + 10);
        } else {
            $pdf->SetFont('times', 'B', 16);
            $pdf->Cell(0, 10, 'Imagen no disponible.', 0, 1, 'C');
            // Actualizar la posición Y para el texto
            $pdf->SetY($pdf->GetY() + 10);
        }

        // Datos de la mascota
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(72, 10, 'Nombre de la Mascota: ', 0, 0, 'L');
        $pdf->SetFont('times', '', 20);
        $pdf->Cell(40, 10, $row["nombre_mascota"], 0, 1, 'L');

        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(20, 10, 'Raza: ', 0, 0, 'L');
        $pdf->SetFont('times', '', 20);
        $pdf->Cell(0, 10, $row["raza"], 0, 1, 'L');

        $pdf->SetFont('times', 'B', 20);

// Ancho total de la línea: ajusta estos valores según el ancho de la página y la cantidad de espacio que necesitas
$anchoEtiquetaEdad = 20; // Ancho para la etiqueta "Edad"
$anchoContenidoEdad = 40; // Ancho para el contenido de la edad
$anchoEtiquetaPerdida = 59; // Ancho para la etiqueta "Fecha de Pérdida"
$anchoContenidoPerdida = 60; // Ancho para el contenido de la fecha de pérdida

// Etiqueta y contenido de "Edad"
$pdf->Cell($anchoEtiquetaEdad, 10, 'Edad: ', 0, 0, 'L');
$pdf->SetFont('times', '', 20);
$edadCompleta = $row["edad"] . ' ' . $row["unidad_t"];
$pdf->Cell($anchoContenidoEdad, 10, $edadCompleta, 0, 0, 'L');

// Etiqueta y contenido de "Fecha de Pérdida"
$pdf->SetFont('times', 'B', 20);
$pdf->Cell($anchoEtiquetaPerdida, 10, 'Fecha de Pérdida: ', 0, 0, 'L');
$pdf->SetFont('times', '', 20);
$pdf->Cell($anchoContenidoPerdida, 10, $row["fecha_perdida"], 0, 1, 'L');


        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(60, 10, 'Lugar de Pérdida: ', 0, 0, 'L');
        $pdf->SetFont('times', '', 20);
        // Ajustar el texto del lugar de pérdida
        $pdf->MultiCell(0, 10, $row["lugar_perdida"], 0, 'L');

        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(30, 10, 'Contacto: ', 0, 0, 'L');
        $pdf->SetFont('times', '', 20);
        $pdf->Cell(60, 10, $row["contacto"], 0, 1, 'L');

        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(27, 10, 'Género: ', 0, 0, 'L');
        $pdf->SetFont('times', '', 20);
        $pdf->Cell(0, 10, $row["genero"], 0, 1, 'L');

        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(59, 10, 'Señas Particulares: ', 0, 0, 'L');
        $pdf->SetFont('times', '', 20);
        $pdf->MultiCell(0, 10, $row["señas_particulares"], 0, 'L');

        // Agregar dirección del dueño con ajuste automático
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(65, 10, 'Dirección del Dueño: ', 0, 0, 'L');
        $pdf->SetFont('times', '', 20);
        // Ajustar la dirección del dueño en múltiples líneas si es necesario
        $pdf->MultiCell(0, 10, $row["dir_dueño"], 0, 'L');

        $pdf->Output('Boletin_Busqueda_Mascotas.pdf', 'I');
    } else {
        echo "No se encontró ninguna mascota con el ID proporcionado.";
    }
} else {
    echo "ID de mascota no válido.";
}

$conn->close();
?>
