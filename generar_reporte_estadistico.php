<?php
// Desactivar toda notificación de errores para evitar salidas no deseadas
error_reporting(0);
ini_set('display_errors', 0);

// Iniciar buffer de salida al principio
ob_start();

// Asegurarse de que no haya salida antes de TCPDF
require_once 'includes/database.php';
require_once 'includes/auth_check.php';
require_once 'vendor/autoload.php'; // Asegúrate de que TCPDF esté instalado

// Desactivar la compresión de salida
ini_set('zlib.output_compression', 'Off');
ini_set('implicit_flush', 'On');
set_time_limit(0);

// Verificar si se ha especificado un período
$periodoDefault = 'mensual';
$periodo = isset($_GET['periodo']) ? strtolower($_GET['periodo']) : $periodoDefault;

// Validar período
$validPeriods = ['diario', 'semanal', 'mensual', 'anual'];
if (!in_array($periodo, $validPeriods)) {
    $periodo = $periodoDefault;
}

// Configurar cláusula WHERE y títulos según el período
switch ($periodo) {
    case 'diario':
        $whereClause = "DATE(p.fecha_prestamo) = CURDATE()";
        $titulo = "Reporte Diario de Herramientas";
        $subtitulo = "Fecha: " . date('d/m/Y');
        break;
    case 'semanal':
        $whereClause = "YEARWEEK(p.fecha_prestamo, 1) = YEARWEEK(CURDATE(), 1)";
        $titulo = "Reporte Semanal de Herramientas";
        $subtitulo = "Semana: " . date('W') . " del año " . date('Y');
        break;
    case 'mensual':
        $whereClause = "YEAR(p.fecha_prestamo) = YEAR(CURDATE()) AND MONTH(p.fecha_prestamo) = MONTH(CURDATE())";
        $titulo = "Reporte Mensual de Herramientas";
        $subtitulo = "Mes: " . date('F Y');
        break;
    case 'anual':
        $whereClause = "YEAR(p.fecha_prestamo) = YEAR(CURDATE())";
        $titulo = "Reporte Anual de Herramientas";
        $subtitulo = "Año: " . date('Y');
        break;
    default:
        $whereClause = "YEAR(p.fecha_prestamo) = YEAR(CURDATE()) AND MONTH(p.fecha_prestamo) = MONTH(CURDATE())";
        $titulo = "Reporte Mensual de Herramientas";
        $subtitulo = "Mes: " . date('F Y');
        $periodo = 'mensual';
}

try {
    // Conexión a la base de datos
    $db = new PDO('mysql:host=localhost;dbname=diesel', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ]);

    // Consultas SQL
    $queryHerramientasNoConsumibles = "
        SELECT 
            hnc.nombre AS herramienta,
            COUNT(p.id) AS total_prestamos,
            COALESCE(SUM(p.cantidad), 0) AS total_unidades
        FROM prestamos p
        JOIN herramientas_no_consumibles hnc ON p.herramienta_id = hnc.id
        WHERE p.herramienta_tipo = 'no_consumible'
        AND $whereClause
        GROUP BY p.herramienta_id
        ORDER BY total_prestamos DESC
        LIMIT 10";

    $queryHerramientasConsumibles = "
        SELECT 
            hc.nombre AS herramienta,
            COUNT(p.id) AS total_prestamos,
            COALESCE(SUM(p.cantidad), 0) AS total_unidades
        FROM prestamos p
        JOIN herramientas_consumibles hc ON p.herramienta_id = hc.id
        WHERE p.herramienta_tipo = 'consumible'
        AND $whereClause
        GROUP BY p.herramienta_id
        ORDER BY total_unidades DESC
        LIMIT 10";

    $queryAprendices = "
        SELECT 
            a.nombre AS aprendiz,
            COUNT(p.id) AS total_prestamos
        FROM prestamos p
        JOIN aprendices a ON p.id_aprendiz = a.id
        WHERE $whereClause
        GROUP BY p.id_aprendiz
        ORDER BY total_prestamos DESC
        LIMIT 10";

    // Ejecutar consultas
    $stmtHerramientasNoConsumibles = $db->query($queryHerramientasNoConsumibles);
    $herramientasNoConsumibles = $stmtHerramientasNoConsumibles->fetchAll();

    $stmtHerramientasConsumibles = $db->query($queryHerramientasConsumibles);
    $herramientasConsumibles = $stmtHerramientasConsumibles->fetchAll();

    $stmtAprendices = $db->query($queryAprendices);
    $aprendicesTop = $stmtAprendices->fetchAll();

    // Verificar si las consultas devolvieron datos
    if (empty($herramientasNoConsumibles) && empty($herramientasConsumibles) && empty($aprendicesTop)) {
        throw new Exception("No se encontraron datos para el período especificado: $periodo");
    }

    // Limpiar buffer de salida
    ob_end_clean();

    // Crear PDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Configurar documento
    $pdf->SetCreator('SGSDIESEL');
    $pdf->SetAuthor('Sistema de Gestión SENA');
    $pdf->SetTitle($titulo);
    $pdf->SetSubject('Reporte Estadístico');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Añadir página
    $pdf->AddPage();

    // Establecer fuente
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->Cell(0, 10, $subtitulo, 0, 1, 'C');
    $pdf->Ln(5);

    // Herramientas No Consumibles
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Herramientas No Consumibles Más Prestadas', 0, 1, 'L');

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 7, 'Herramienta', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Total Préstamos', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Total Unidades', 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 10);
    if (empty($herramientasNoConsumibles)) {
        $pdf->Cell(190, 7, 'No hay datos disponibles', 1, 1, 'C');
    } else {
        foreach ($herramientasNoConsumibles as $herramienta) {
            $pdf->Cell(90, 7, $herramienta['herramienta'], 1, 0, 'L');
            $pdf->Cell(50, 7, $herramienta['total_prestamos'], 1, 0, 'C');
            $pdf->Cell(50, 7, $herramienta['total_unidades'], 1, 1, 'C');
        }
    }

    $pdf->Ln(10);

    // Herramientas Consumibles
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Herramientas Consumibles Más Utilizadas', 0, 1, 'L');

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 7, 'Herramienta', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Total Préstamos', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Total Unidades', 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 10);
    if (empty($herramientasConsumibles)) {
        $pdf->Cell(190, 7, 'No hay datos disponibles', 1, 1, 'C');
    } else {
        foreach ($herramientasConsumibles as $herramienta) {
            $pdf->Cell(90, 7, $herramienta['herramienta'], 1, 0, 'L');
            $pdf->Cell(50, 7, $herramienta['total_prestamos'], 1, 0, 'C');
            $pdf->Cell(50, 7, $herramienta['total_unidades'], 1, 1, 'C');
        }
    }

    $pdf->Ln(10);

    // Aprendices con más préstamos
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Aprendices con Más Préstamos', 0, 1, 'L');

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(140, 7, 'Aprendiz', 1, 0, 'C');
    $pdf->Cell(50, 7, 'Total Préstamos', 1, 1, 'C');

    $pdf->SetFont('helvetica', '', 10);
    if (empty($aprendicesTop)) {
        $pdf->Cell(190, 7, 'No hay datos disponibles', 1, 1, 'C');
    } else {
        foreach ($aprendicesTop as $aprendiz) {
            $pdf->Cell(140, 7, $aprendiz['aprendiz'], 1, 0, 'L');
            $pdf->Cell(50, 7, $aprendiz['total_prestamos'], 1, 1, 'C');
        }
    }

    // Generar gráficos
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Gráficos Estadísticos', 0, 1, 'C');

    // Preparar datos para gráficos
    $datosNoConsumibles = [];
    $labelsNoConsumibles = [];
    foreach ($herramientasNoConsumibles as $herramienta) {
        $datosNoConsumibles[] = $herramienta['total_prestamos'];
        $labelsNoConsumibles[] = $herramienta['herramienta'];
    }

    $datosConsumibles = [];
    $labelsConsumibles = [];
    foreach ($herramientasConsumibles as $herramienta) {
        $datosConsumibles[] = $herramienta['total_unidades'];
        $labelsConsumibles[] = $herramienta['herramienta'];
    }

    // Gráfico para herramientas no consumibles
    if (!empty($datosNoConsumibles)) {
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Herramientas No Consumibles Más Prestadas', 0, 1, 'L');

        $width = 180;
        $height = 80;
        $x = 15;
        $y = $pdf->GetY();

        $pdf->Line($x, $y, $x, $y + $height);
        $pdf->Line($x, $y + $height, $x + $width, $y + $height);

        $maxValue = max($datosNoConsumibles) ?: 1;
        $barWidth = $width / count($datosNoConsumibles);

        for ($i = 0; $i < count($datosNoConsumibles); $i++) {
            $barHeight = ($datosNoConsumibles[$i] / $maxValue) * $height;
            $barX = $x + ($i * $barWidth);
            $barY = $y + $height - $barHeight;

            $pdf->SetFillColor(41, 128, 185);
            $pdf->Rect($barX, $barY, $barWidth - 2, $barHeight, 'F');

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($barX, $barY - 10);
            $pdf->Cell($barWidth - 2, 10, $datosNoConsumibles[$i], 0, 0, 'C');

            $pdf->SetXY($barX, $y + $height + 2);
            $pdf->Cell($barWidth - 2, 10, substr($labelsNoConsumibles[$i], 0, 10), 0, 0, 'C');
        }
    }

    // Gráfico para herramientas consumibles
    if (!empty($datosConsumibles)) {
        $pdf->Ln(100);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Herramientas Consumibles Más Utilizadas', 0, 1, 'L');
        $y = $pdf->GetY();

        $pdf->Line($x, $y, $x, $y + $height);
        $pdf->Line($x, $y + $height, $x + $width, $y + $height);

        $maxValue = max($datosConsumibles) ?: 1;
        $barWidth = $width / count($datosConsumibles);

        for ($i = 0; $i < count($datosConsumibles); $i++) {
            $barHeight = ($datosConsumibles[$i] / $maxValue) * $height;
            $barX = $x + ($i * $barWidth);
            $barY = $y + $height - $barHeight;

            $pdf->SetFillColor(231, 76, 60);
            $pdf->Rect($barX, $barY, $barWidth - 2, $barHeight, 'F');

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($barX, $barY - 10);
            $pdf->Cell($barWidth - 2, 10, $datosConsumibles[$i], 0, 0, 'C');

            $pdf->SetXY($barX, $y + $height + 2);
            $pdf->Cell($barWidth - 2, 10, substr($labelsConsumibles[$i], 0, 10), 0, 0, 'C');
        }
    }

    // Resumen estadístico
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Resumen Estadístico', 0, 1, 'C');

    $totalPrestamosNoConsumibles = array_sum(array_column($herramientasNoConsumibles, 'total_prestamos'));
    $totalUnidadesNoConsumibles = array_sum(array_column($herramientasNoConsumibles, 'total_unidades'));
    $totalPrestamosConsumibles = array_sum(array_column($herramientasConsumibles, 'total_prestamos'));
    $totalUnidadesConsumibles = array_sum(array_column($herramientasConsumibles, 'total_unidades'));

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Totales por Categoría', 0, 1, 'L');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(100, 7, 'Total préstamos de herramientas no consumibles:', 0, 0, 'L');
    $pdf->Cell(0, 7, $totalPrestamosNoConsumibles, 0, 1, 'L');

    $pdf->Cell(100, 7, 'Total unidades prestadas no consumibles:', 0, 0, 'L');
    $pdf->Cell(0, 7, $totalUnidadesNoConsumibles, 0, 1, 'L');

    $pdf->Cell(100, 7, 'Total préstamos de herramientas consumibles:', 0, 0, 'L');
    $pdf->Cell(0, 7, $totalPrestamosConsumibles, 0, 1, 'L');

    $pdf->Cell(100, 7, 'Total unidades consumidas:', 0, 0, 'L');
    $pdf->Cell(0, 7, $totalUnidadesConsumibles, 0, 1, 'L');

    $pdf->Ln(10);

    // Pie de página
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 10, 'Reporte generado el ' . date('d/m/Y H:i:s') . ' por el Sistema de Gestión SGSDIESEL', 0, 1, 'C');

    // Generar nombre del archivo
    $nombreArchivo = 'Reporte_' . $periodo . '_' . date('Y-m-d') . '.pdf';

    // Salida del PDF
    $pdf->Output($nombreArchivo, 'I');

} catch (PDOException $e) {
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error en la base de datos: " . $e->getMessage();
    exit;
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error general: " . $e->getMessage();
    exit;
}
?>