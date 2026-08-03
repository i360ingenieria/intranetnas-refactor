<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LibroController extends Controller
{
    /**
     * Muestra el libro interactivo
     */
    public function index()
    {
        // Datos de las imágenes (título, descripción, archivo)
        $imagenes = [
            [
                'titulo' => 'Digitar en la busqueda',
                'descripcion' => 'lista de Carpetas',
                'archivo' => '1.png',
                'efecto' => 'effect-1'
            ],
            [
                'titulo' => 'Boton verde → VER EL DOCUMENTO',
                'descripcion' => 'ACIONES DE LOS BOTONES',
                'archivo' => '3.png',
                'efecto' => 'effect-2'
            ],
            [
                'titulo' => 'VER DOCUMENTO',
                'descripcion' => 'DESCARGA O',
                'archivo' => '4.png',
                'efecto' => ''
            ],
            [
                'titulo' => 'SUBIR DE NIVEL ES PARA DEVOLVER POR LAS CARPETAS',
                'descripcion' => 'BUSQUEDA EN CADA CARPETA',
                'archivo' => '51.png',
                'efecto' => ''
            ],
            // Agrega más imágenes según necesites (hasta 10)
            [
                'titulo' => 'Azul Profundo',
                'descripcion' => 'Olas infinitas',
                'archivo' => 'https://images.unsplash.com/photo-1439405326854-014607f694d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'externa' => true
            ],
            [
                'titulo' => 'Jardín Secreto',
                'descripcion' => 'Flores silvestres',
                'archivo' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'externa' => true
            ],
            [
                'titulo' => 'Reflejos de Paz',
                'descripcion' => 'Lago cristalino',
                'archivo' => 'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'externa' => true
            ],
            [
                'titulo' => 'Memorias Doradas',
                'descripcion' => 'Atardecer vintage',
                'archivo' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'externa' => true
            ],
            [
                'titulo' => 'Agua Viva',
                'descripcion' => 'Cascada mágica',
                'archivo' => 'https://images.unsplash.com/photo-1426604966848-d7adac402bff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'externa' => true
            ],
            [
                'titulo' => 'Luces del Norte',
                'descripcion' => 'Aurora boreal',
                'archivo' => 'https://images.unsplash.com/photo-1518173946687-a4c8892bbd9f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'externa' => true
            ],
        ];

        return view('tutorial', compact('imagenes'));
    }
}