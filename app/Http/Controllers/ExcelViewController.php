<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelViewController extends Controller
{
    private $maxRowsToLoad = 5000; // Máximo de filas a mostrar
    
    public function ver($ruta)
    {
        $rutaArchivo = '/' . ltrim($ruta, '/');
        
        try {
            if (!file_exists($rutaArchivo)) {
                return "Error: Archivo no encontrado - $rutaArchivo";
            }
            
            // Detectar si es un archivo grande (más de 5MB)
            $fileSize = filesize($rutaArchivo);
            $isLargeFile = $fileSize > 5 * 1024 * 1024; // 5MB
            
            // Aumentar memoria solo si es necesario
            if ($isLargeFile) {
                ini_set('memory_limit', '1024M');
                set_time_limit(300);
            }
            
            $spreadsheet = IOFactory::load($rutaArchivo);
            $sheets = [];
            
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                // Obtener todas las filas
                $allData = $sheet->toArray();
                $headers = !empty($allData) ? array_shift($allData) : [];
                
                $totalRows = count($allData);
                $hasWarning = false;
                $dataToShow = $allData;
                
                // Si es archivo grande o la hoja tiene muchas filas, limitar
                if ($isLargeFile || $totalRows > $this->maxRowsToLoad) {
                    $hasWarning = true;
                    $dataToShow = array_slice($allData, 0, $this->maxRowsToLoad);
                }
                
                $sheets[] = [
                    'name' => $sheet->getTitle(),
                    'headers' => $headers,
                    'rows' => $dataToShow,
                    'totalRows' => $totalRows,
                    'warning' => $hasWarning
                ];
            }
            
            $filename = basename($rutaArchivo);
            
            return view('visor', [
                'filename' => $filename,
                'sheets' => $sheets,
                'totalSheets' => count($sheets),
                'isLargeFile' => $isLargeFile
            ]);
            
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}