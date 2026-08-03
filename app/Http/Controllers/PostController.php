<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Models\Respuesta;
use App\Models\Logs;

class PostController extends Controller
{
    // En tu controlador - CORREGIDO
    public function index()
    {
        // dd($logs, $this->getMsg()); // Verificar que $logs y $respuestas contienen los datos esperados
        $logs = Logs::with('respuestas')->latest()->get();
        return view('post', compact('logs'));
    }

    public function getMsg()
    {
        $logs = Logs::with('respuestas')->latest()->get();
        return view('portails.mensajes', compact('logs')); // solo el parcial
    }

    public function store(Request $request)
    {
        $ip = $request->ip();

        DB::table('logs')->insert([
            'ip_address' => $request->ip(),
            'mensaje'    => $request->input('mensaje'),
            'estado'     => 'activo',
            'tipo'       => $request->input('tipo'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        return back()->with('success', 'Tu IP ha sido registrada: ' . $ip);
    }

    public function guardarRespuesta(Request $request, $logId)
    {
        $request->validate([
            'mensaje' => 'required|string|max:500'
        ]);

        $log = Logs::findOrFail($logId);

        $respuesta = Respuesta::create([
            'idr' => $log->id,
            'respuesta' => $request->mensaje,
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
            'log_id' => $log->id // Asegúrate de que este campo exista en tu tabla 'resp'


        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'respuesta' => $respuesta,
                'html' => view('partials.respuesta', compact('respuestas', 'logs'))->render()
            ]);
        }
        return back()->with('success', 'Respuesta guardada');
    }

    // Obtener todas las respuestas de un log
    public function getLogs()
    {
        $logs =  DB::table('logs')->latest()->get();
        return $logs;
    }
}
