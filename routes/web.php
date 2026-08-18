<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FichatecfController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\ExcelViewController;
 use App\Http\Controllers\Api\PlatformController;
 


// Route::get('/', function () {
//     return view('index');
// })->name('home');

Route::get('/libro', [LibroController::class, 'index'])->name('libro');

Route::get('/iframe', function () {
    return view('iframe');
})->name('iframe');

Route::get('/directorio', function () {

    $path = storage_path('CPYDIRECTORIOx.csv');
    $contactos = [];

    if (($handle = fopen($path, 'r')) !== false) {

        $encabezados = fgetcsv($handle, 0, ';');
 
        $encabezados = array_map(function ($campo) {
            return trim(preg_replace('/^\xEF\xBB\xBF/u', '', $campo));
        }, $encabezados);

         while (($fila = fgetcsv($handle, 0, ';')) !== false) {

            if (count(array_filter($fila)) === 0) {
                continue;
            }

            $fila = array_pad($fila, count($encabezados), '');
            $fila = array_slice($fila, 0, count($encabezados));

            $contacto = array_combine($encabezados, $fila);

            $contactos[] = [
                'Nombre'     => $contacto['NOMBRE'] ?? '',
                'Cargo'      => $contacto['CARGO'] ?? '',
                'Email'      => $contacto['CORREO INSTITUCIONAL'] ?? '',
                'Teléfono'   => $contacto['TELEFONO INSTITUCIONAL 3302507'] ?? '',
                'Ubicacion'  => $contacto['DEPENDENCIA'] ?? '',
            ];
        }

        fclose($handle);
    }

    return view('directorio', compact('contactos'));

})->name('directorio');

Route::get('/documentos', function () {
    return view('documentos');
})->name('documentos');

// Route::get('/fichatecnica', function () {
//     return view('fichatecnica');
// })->name('fichatecnica');

// Rutas para el foro
Route::get('/post', [PostController::class, 'index'])->name('post');
Route::post('/guardar-ip', [PostController::class, 'store'])->name('guardar.ip');
Route::post('/guardar-respuesta/{logId}', [PostController::class, 'guardarRespuesta'])->name('guardar.respuesta');
Route::get('/respuestas', [PostController::class, 'getMsg'])->name('get.respuestas');
Route::get('/mensajes', [PostController::class, 'getMsg'])->name('get.mensajes');
  

// Rutas para archivos
Route::get('/archivos/buscar', [ArchivoController::class, 'buscar']);
Route::get('/archivos/ver/{id}', [ArchivoController::class, 'ver']);
Route::get('/archivos/descargar/{id}', [ArchivoController::class, 'descargar']);
Route::get('/archivos/ver/{id}', [ArchivoController::class, 'ver']) ->name('archivos.ver');

// Rutas para Ficha Técnica mercadeo
// Route::get('/fichatecnica', [FichatecfController::class, 'index'])->name('fichatecnica');
Route::get('/fichatecnica/buscar', [FichatecfController::class, 'buscar']);
Route::get('/fichatecnica/descargar/{id}', [FichatecfController::class, 'descargar'])->name('fichatecnica.descargar');  
Route::get('/fichatecnica/ver/{id}', [FichatecfController::class, 'ver'])->name('fichatecnica.ver');

// Ruta para ver Excel desde ruta NAS de mercadeo
// ✅ Ruta para ver Excel - AGREGAR ESTA
//Route::get('/fichatecnica/excel/ver/{id}', [ExcelViewController::class, 'ver']);

// Si quieres también soportar la ruta sin /fichatecnica
Route::get('/excel/ver', [ExcelViewController::class, 'ver'])
    ->name('excel.ver');

// Ruta para probar la instalación de PhpSpreadsheet
Route::get('/test-phpspreadsheet', function() {
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Test');
        
        $tempFile = storage_path('app/test.xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);
        
        $loaded = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
        $data = $loaded->getActiveSheet()->toArray();
        
        return response()->json([
            'success' => true,
            'message' => 'PhpSpreadsheet funciona correctamente',
            'test_data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});
Route::get('/platforms',[PlatformController::class,'index']);
// Route::post('/guardar-respuesta/{logId}', [PostController::class, 'guardarRespuesta'])
//     ->name('guardar-respuesta');


Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');