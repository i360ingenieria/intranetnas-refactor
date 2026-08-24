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
     * Visualizar archivo Excel como JSON para React.
     *
     * GET /excel/ver/{id}
     */
    public function ver(Request $request)
{
        $id = $request->query('id');

    if (!$id) {
        return response()->json([
            'message' => 'Debe proporcionar el ID del archivo.',
        ], 400);
    }

        // =====================================================
        // 1. BUSCAR ARCHIVO
        // =====================================================

        $archivo = FichaTec::findOrFail($id);

        // =====================================================
        // 2. VALIDAR TIPO
        // =====================================================

        if ($archivo->tipo !== 'archivo') {

            return response()->json([
                'message' =>
                    'El registro no corresponde a un archivo.',
            ], 404);
        }

        // =====================================================
        // 3. VALIDAR EXTENSIÓN
        // =====================================================

        $extension =
            strtolower(
                $archivo->extension
            );

        if (
            !in_array(
                $extension,
                [
                    'xls',
                    'xlsx',
                    'xlsm',
                    'csv',
                ],
                true
            )
        ) {

            return response()->json([
                'message' =>
                    'El archivo no es compatible con el visor de Excel.',
            ], 403);
        }

        // =====================================================
        // 4. RUTA REAL
        // =====================================================

        $rutaArchivo =
            $archivo->ruta;

        if (
            !is_file($rutaArchivo)
        ) {

            return response()->json([
                'message' =>
                    'El archivo no existe físicamente en el NAS.',
            ], 404);
        }

        // =====================================================
        // 5. TAMAÑO
        // =====================================================

        $fileSize =
            filesize($rutaArchivo);

        $isLargeFile =
            $fileSize > (5 * 1024 * 1024);

        // =====================================================
        // 6. ARCHIVOS GRANDES
        // =====================================================

        if ($isLargeFile) {

            ini_set(
                'memory_limit',
                '1024M'
            );

            set_time_limit(300);
        }

        // =====================================================
        // 7. CARGAR EXCEL
        // =====================================================

        try {

            $spreadsheet =
                IOFactory::load(
                    $rutaArchivo
                );

        } catch (\Throwable $e) {

            return response()->json([
                'filename' =>
                    $archivo->nombre,

                'sheets' => [],

                'totalSheets' => 0,

                'isLargeFile' =>
                    $isLargeFile,

                'error' =>
                    'No fue posible abrir el archivo Excel: '
                    . $e->getMessage(),

            ], 500);
        }

        // =====================================================
        // 8. PROCESAR HOJAS
        // =====================================================

        $sheets = [];

        foreach (
            $spreadsheet->getAllSheets()
            as $sheet
        ) {

            $allData =
                $sheet->toArray();

            // -------------------------------------------------
            // ENCABEZADOS
            // -------------------------------------------------

            $headers = [];

            if (
                !empty($allData)
            ) {

                $headers =
                    array_shift(
                        $allData
                    );
            }

            // -------------------------------------------------
            // TOTAL
            // -------------------------------------------------

            $totalRows =
                count($allData);

            // -------------------------------------------------
            // LIMITAR
            // -------------------------------------------------

            $hasWarning =
                false;

            if (
                $isLargeFile ||
                $totalRows >
                    $this->maxRowsToLoad
            ) {

                $hasWarning =
                    true;

                $dataToShow =
                    array_slice(
                        $allData,
                        0,
                        $this->maxRowsToLoad
                    );

            } else {

                $dataToShow =
                    $allData;
            }

            // -------------------------------------------------
            // HOJA
            // -------------------------------------------------

            $sheets[] = [

                'name' =>
                    $sheet->getTitle(),

                'headers' =>
                    $headers,

                'rows' =>
                    $dataToShow,

                'totalRows' =>
                    $totalRows,

                'warning' =>
                    $hasWarning,

            ];
        }

        // =====================================================
        // 9. LIBERAR MEMORIA
        // =====================================================

        $spreadsheet
            ->disconnectWorksheets();

        unset(
            $spreadsheet
        );

        // =====================================================
        // 10. JSON
        // =====================================================

        return response()->json([

            'filename' =>
                $archivo->nombre,

            'sheets' =>
                $sheets,

            'totalSheets' =>
                count($sheets),

            'isLargeFile' =>
                $isLargeFile,

            'fileId' =>
                $archivo->id,

        ]);
    }
}