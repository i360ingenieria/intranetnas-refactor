<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Archivo;
 use Symfony\Component\HttpFoundation\StreamedResponse;

class ArchivoController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function buscar(Request $request)
    {
        $q = trim($request->q ?? '');
        $basePath = rtrim($request->basePath ?? '', '/');
        
        if (empty($basePath)) {
            $basePath = '/mnt/intranet/sistema gestion de calidad';
        }

        $query = Archivo::where('ruta', 'like', $basePath . '%')
                        ->where('nombre', 'NOT LIKE', '~$%');

        if ($q !== '') {
            $resultados = $query->where('nombre', 'like', "%{$q}%")->get();
        } else {
            $nivelActual = substr_count($basePath, '/');
            $resultados = $query->whereRaw("LENGTH(ruta) - LENGTH(REPLACE(ruta, '/', '')) = ?", [$nivelActual + 1])
                ->orderByRaw("tipo = 'carpeta' DESC")
                ->orderBy('nombre')
                ->get();
        }

        // --- EL FILTRO DEFINITIVO ---
        $resultadosUnicos = $resultados
        ->groupBy(function ($item) {
            return strtolower(trim($item->nombre)) . '|' . $item->tipo;
        })
        ->map(function ($grupo) {
            if ($grupo->count() === 1) {
                return $grupo->first();
            }
            return $grupo->sortByDesc(function ($item) {
                return $item->modified ?? '1900-01-01';
            })->first();
        })
        ->values();

        // --- ORDENACIÓN MEJORADA (con orden numérico) ---
        $final = $resultadosUnicos->sort(function($a, $b) use ($q) {
            $tipoA = $a->tipo === 'carpeta' ? 0 : 1;
            $tipoB = $b->tipo === 'carpeta' ? 0 : 1;
            if ($tipoA != $tipoB) return $tipoA - $tipoB;

            if ($q !== '') {
                $aStarts = stripos(trim($a->nombre), $q) === 0;
                $bStarts = stripos(trim($b->nombre), $q) === 0;
                if ($aStarts != $bStarts) return $aStarts ? -1 : 1;
            }

            // 🆕 ORDEN NUMÉRICO PARA CARPETAS CON NÚMEROS (1, 2, 10, 11, 20)
            $nombreA = trim($a->nombre);
            $nombreB = trim($b->nombre);

            // Extraer número al inicio (ej: "1. MANUAL" → 1)
            preg_match('/^(\d+)\.?\s*/', $nombreA, $matchA);
            preg_match('/^(\d+)\.?\s*/', $nombreB, $matchB);

            if ($matchA && $matchB) {
                // Ambos tienen número → ordenar numéricamente
                $numA = (int)$matchA[1];
                $numB = (int)$matchB[1];
                if ($numA != $numB) {
                    return $numA - $numB;
                }
                // Si mismo número, ordenar por el texto después del número
                $textoA = preg_replace('/^\d+\.?\s*/', '', $nombreA);
                $textoB = preg_replace('/^\d+\.?\s*/', '', $nombreB);
                return strcasecmp($textoA, $textoB);
            }

            if ($matchA && !$matchB) {
                return -1; // Los que tienen número van primero
            }

            if (!$matchA && $matchB) {
                return 1; // Los que tienen número van primero
            }

            // Si ninguno tiene número, orden alfabético normal
            return strcasecmp($nombreA, $nombreB);
        })->take(500);

        // --- FORMATEO ---
        $archivosFormateados = $final->map(function($archivo) use ($basePath) {
            $rutaLimpia = rtrim($archivo->ruta, '/');
            $partes = explode('/', $rutaLimpia);
            array_pop($partes);
            $ubicacionPadre = implode('/', $partes);

            return [
                'id'            => $archivo->id,
                'nombre'        => trim($archivo->nombre),
                'ruta'          => $archivo->ruta,
                'ubicacion'     => str_replace($basePath, '', $ubicacionPadre) ?: '/',
                'tipo'          => $archivo->tipo,
                'extension'     => $archivo->extension ?? '',
                'modified'      => $archivo->modified,
                'size'          => $archivo->size ?? 0,
                'ruta_relativa' => ltrim(str_replace($basePath, '', $archivo->ruta), '/')
            ];
        })->values();

        return response()->json([
            'data' => $archivosFormateados,
            'recordsTotal' => $archivosFormateados->count(),
            'recordsFiltered' => $archivosFormateados->count()
        ]);
    }

    public function descargar($id)
    {
       
        $archivo = Archivo::where('id', $id)
            ->where('tipo', 'archivo')
            ->firstOrFail();

       // abort_unless(is_file($archivo->ruta), 404);

        return response()->download(
            $archivo->ruta,
            $archivo->nombre
        );
    }

    public function carpeta(Request $request)
    {
        $ruta = rtrim($request->ruta, '/');

        // Validar que la carpeta exista en BD
        $carpeta = Archivo::where('ruta', $ruta)
            ->where('tipo', 'carpeta')
            ->firstOrFail();

        // Obtener hijos directos
        $archivos = Archivo::where('ruta', 'like', $ruta . '/%')
            ->whereRaw(
                "LENGTH(REPLACE(ruta, ?, '')) - LENGTH(REPLACE(REPLACE(ruta, ?, ''), '/', '')) = 1",
                [$ruta . '/', $ruta . '/']
            )
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'tipo', 'ruta']);

        return response()->json($archivos);
    }
    
    public function ver($id)
    {
        $archivo = Archivo::findOrFail($id);
         // 🔐 Solo PDFs
        if (strtolower($archivo->extension) !== 'pdf') {
            abort(403, 'No es un PDF');
        }

        // 📂 Validar existencia real en NAS
        if (!is_file($archivo->ruta)) {
            abort(404, 'Archivo no encontrado en NAS');
        }

        // 🧠 Limpiar output buffer (CLAVE para PDFs)
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

    public function buscarGlobal(Request $request)
    {
        $q = trim($request->q ?? '');

        if ($q === '') {
            return response()->json([
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $resultados = Archivo::where('nombre', 'NOT LIKE', '~$%')
            ->where('nombre', 'like', "%{$q}%")
            ->orderByRaw("tipo = 'carpeta' DESC")
            ->orderBy('nombre')
            ->limit(500)
            ->get();

        $archivosFormateados = $resultados->map(function ($archivo) {

            return [
                'id'            => $archivo->id,
                'nombre'        => trim($archivo->nombre),
                'ruta'          => $archivo->ruta,
                'ubicacion'     => dirname($archivo->ruta),
                'tipo'          => $archivo->tipo,
                'extension'     => $archivo->extension ?? '',
                'modified'      => $archivo->modified,
                'size'          => $archivo->size ?? 0,
                'ruta_relativa' => ltrim($archivo->ruta, '/')
            ];

        })->values();

        return response()->json([
            'data' => $archivosFormateados,
            'recordsTotal' => $archivosFormateados->count(),
            'recordsFiltered' => $archivosFormateados->count()
        ]);
    }


}
