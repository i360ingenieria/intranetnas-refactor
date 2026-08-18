<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\FichaTec;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'archivos' => Archivo::where('tipo', 'archivo')->count(),
            'carpetas' => Archivo::where('tipo', 'carpeta')->count(),
            'fichas'   => FichaTec::count(),
            'ultima_actualizacion' => Archivo::max('modified')
        ]);
    }
}