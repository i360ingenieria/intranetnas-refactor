<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plataforma;

class PlatformController extends Controller
{

    public function index()
    {
        return Plataforma::where('activo',1)
                ->orderBy('categoria')
                ->orderBy('orden')
                ->get();
    }
}
