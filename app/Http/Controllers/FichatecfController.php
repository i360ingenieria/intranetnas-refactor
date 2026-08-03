<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FichaTec;

class FichatecfController extends Controller
{
    // 🔹 Ruta raíz del módulo MERCADEO
    private $root = '/mnt/nas_pcmercadeo%';

    public function index()
    {
        return view('fichatecnica');
    }

public function buscar(Request $request)
{
    $q = trim($request->q ?? '');
    $basePath = rtrim($request->basePath ?? '/mnt/nas_pcmercadeo/', '/');

    $query = FichaTec::query();

    // ==================================================
    // 🔍 BÚSQUEDA GLOBAL
    // ==================================================

    if ($q !== '') {

        $query->where(function ($subquery) use ($q) {

            $subquery->where('ruta', 'like', '/mnt/nas_pcmercadeo%')
                ->where('ruta', 'NOT LIKE', '%@Recycle%')
                ->where('nombre', 'NOT LIKE', '~$%')
                ->where('nombre', '!=', 'Thumbs.db')
                ->where('nombre', 'NOT LIKE', '.~lock.%')
                ->where('nombre', 'like', "%{$q}%");

        });

        $query->orderByRaw("tipo = 'carpeta' DESC")
              ->orderByRaw("
                    CASE
                        WHEN nombre LIKE ? THEN 1
                        WHEN nombre LIKE ? THEN 2
                        ELSE 3
                    END
              ", ["{$q}%", "%{$q}%"]);

    } else {

        // ==================================================
        // 📁 MODO EXPLORADOR
        // ==================================================

        $query->where('ruta', 'like', $basePath . '%')
              ->where('ruta', 'NOT LIKE', '%@Recycle%')
              ->where('nombre', 'NOT LIKE', '~$%')
              ->where('nombre', '!=', 'Thumbs.db')
              ->where('nombre', 'NOT LIKE', '.~lock.%');

        $nivelActual = substr_count($basePath, '/');

        $query->whereRaw(
            "LENGTH(ruta) - LENGTH(REPLACE(ruta, '/', '')) = ?",
            [$nivelActual + 1]
        );

        $query->orderByRaw("tipo = 'carpeta' DESC")
              ->orderBy('nombre');
    }

    // ==================================================
    // OBTENER RESULTADOS
    // ==================================================

    $resultados = $query
        ->limit(500)
        ->get();

    // ==================================================
    // FORMATEAR RESULTADOS
    // (SIN ELIMINAR ARCHIVOS CON EL MISMO NOMBRE)
    // ==================================================

    $archivos = $resultados->map(function ($archivo) {

        $ultimoSlash = strrpos($archivo->ruta, '/');

        $ubicacion = $ultimoSlash !== false
            ? substr($archivo->ruta, 0, $ultimoSlash)
            : '';

        return [
            'id'             => $archivo->id,
            'nombre'         => $archivo->nombre,
            'ruta'           => $archivo->ruta,
            'ubicacion'      => $ubicacion,
            'tipo'           => $archivo->tipo,
            'extension'      => $archivo->extension,
            'modified'       => $archivo->modified,
            'size'           => $archivo->size,
            'ruta_relativa'  => str_replace(
                '/mnt/nas_pcmercadeo/',
                '',
                $archivo->ruta
            )
        ];
    });

    return response()->json([
        'data' => $archivos
    ]);
}

 public function descargar($id)
{
    $archivo = FichaTec::where('id', $id)
        ->where('tipo', 'archivo')
        ->firstOrFail();

    // Solo permitir PDF
    if (strtolower($archivo->extension) !== 'pdf') {
        abort(403, 'La descarga de este tipo de archivo está bloqueada.');
    }

    abort_unless(is_file($archivo->ruta), 404);

    return response()->download(
        $archivo->ruta,
        $archivo->nombre
    );
}

    public function carpeta(Request $request)
    {
        $ruta = $request->ruta
            ? rtrim($request->ruta, '/')
            : $this->root;

        // Validar que exista en BD
        $carpeta = FichaTec::where('ruta', $ruta)
            ->where('tipo', 'carpeta')
            ->firstOrFail();

        // Obtener hijos directos
        $archivos = FichaTec::where('ruta', 'like', $ruta . '/%')
            ->whereRaw(
                "LENGTH(ruta) - LENGTH(REPLACE(ruta, '/', '')) = ?",
                [substr_count($ruta, '/') + 1]
            )
            ->orderByRaw("tipo = 'carpeta' DESC")
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'tipo', 'ruta']);

        return response()->json($archivos);
    }

    public function ver($id)
    {
        $archivo = FichaTec::findOrFail($id);

        // 🔐 Solo PDFs
        if (strtolower($archivo->extension) !== 'pdf') {
            abort(403, 'No es un PDF');
        }

        // 📂 Validar existencia real en NAS
        if (!is_file($archivo->ruta)) {
            abort(404, 'Archivo no encontrado en NAS');
        }

        // 🧠 Limpiar buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        return response()->file(
            $archivo->ruta,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$archivo->nombre.'"'
            ]
        );
    }
}