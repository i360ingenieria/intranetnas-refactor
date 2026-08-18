<?php

namespace App\Http\Controllers;

use App\Models\FichaTec;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelViewController extends Controller
{
    /**
     * Máximo de filas que se muestran por hoja.
     */
    private int $maxRowsToLoad = 5000;

    /**
     * Visualizar archivo Excel.
     *
     * URL:
     * /fichatecnica/excel/ver/{id}
     */
    public function ver($id)
    {
        // =====================================================
        // 1. BUSCAR ARCHIVO EN LA BASE DE DATOS
        // =====================================================

        $archivo = FichaTec::findOrFail($id);

        // =====================================================
        // 2. VALIDAR QUE SEA UN ARCHIVO
        // =====================================================

        if ($archivo->tipo !== 'archivo') {
            abort(404, 'El registro no corresponde a un archivo.');
        }

        // =====================================================
        // 3. VALIDAR EXTENSIÓN
        // =====================================================

        $extension = strtolower($archivo->extension);

        if (!in_array($extension, ['xls', 'xlsx', 'xlsm', 'csv'])) {
            abort(
                403,
                'El archivo no es compatible con el visor de Excel.'
            );
        }

        // =====================================================
        // 4. OBTENER RUTA REAL DEL NAS
        // =====================================================

        $rutaArchivo = $archivo->ruta;

        if (!is_file($rutaArchivo)) {
            abort(
                404,
                'El archivo no existe físicamente en el NAS.'
            );
        }

        // =====================================================
        // 5. INFORMACIÓN DEL ARCHIVO
        // =====================================================

        $fileSize = filesize($rutaArchivo);

        $isLargeFile = $fileSize > (5 * 1024 * 1024);

        // =====================================================
        // 6. CONFIGURACIÓN PARA ARCHIVOS GRANDES
        // =====================================================

        if ($isLargeFile) {

            ini_set('memory_limit', '1024M');

            set_time_limit(300);
        }

        // =====================================================
        // 7. CARGAR EXCEL
        // =====================================================

        try {

            $spreadsheet = IOFactory::load($rutaArchivo);

        } catch (\Throwable $e) {

            return response()->view(
                'visor',
                [
                    'filename' => $archivo->nombre,
                    'sheets' => [],
                    'totalSheets' => 0,
                    'isLargeFile' => $isLargeFile,
                    'error' => 'No fue posible abrir el archivo Excel: '
                        . $e->getMessage(),
                ],
                500
            );
        }

        // =====================================================
        // 8. PROCESAR HOJAS
        // =====================================================

        $sheets = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {

            $allData = $sheet->toArray();

            // -------------------------------------------------
            // Encabezados
            // -------------------------------------------------

            $headers = [];

            if (!empty($allData)) {
                $headers = array_shift($allData);
            }

            // -------------------------------------------------
            // Total de filas
            // -------------------------------------------------

            $totalRows = count($allData);

            // -------------------------------------------------
            // Determinar si se limita
            // -------------------------------------------------

            $hasWarning = false;

            if (
                $isLargeFile ||
                $totalRows > $this->maxRowsToLoad
            ) {

                $hasWarning = true;

                $dataToShow = array_slice(
                    $allData,
                    0,
                    $this->maxRowsToLoad
                );

            } else {

                $dataToShow = $allData;
            }

            // -------------------------------------------------
            // Guardar información de la hoja
            // -------------------------------------------------

            $sheets[] = [

                'name' => $sheet->getTitle(),

                'headers' => $headers,

                'rows' => $dataToShow,

                'totalRows' => $totalRows,

                'warning' => $hasWarning,

            ];
        }

        // =====================================================
        // 9. LIBERAR SPREADSHEET
        // =====================================================

        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        // =====================================================
        // 10. NOMBRE DEL ARCHIVO
        // =====================================================

        $filename = $archivo->nombre;

        // =====================================================
        // 11. ENVIAR AL VISOR
        // =====================================================

        return view('visor', [

            'filename' => $filename,

            'sheets' => $sheets,

            'totalSheets' => count($sheets),

            'isLargeFile' => $isLargeFile,

            'fileId' => $archivo->id,

            'ruta' => $archivo->ruta,

        ]);
    }
}