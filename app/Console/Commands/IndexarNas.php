<?php



namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archivo;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;

class IndexarIntranetNas  extends Command
{
    protected $signature = 'app:indexar-intranet {path=/mnt/intranet}';
    protected $description = 'Indexa carpetas y archivos desde NAS';

    public function handle()
    {
        
        $basePath = $this->argument('path');
        $count = 0;

        $this->info("📂 Indexando sistemas de Gestión: {$basePath}");

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $basePath,
                FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            

            $realPath = $item->getPathname(); // 👈 NO getRealPath (CIFS bug)
            if (!$realPath) continue;

            $path = utf8_encode($realPath);

            /**
             * =========================
             * 📁 CARPETAS (PRIMERO SIEMPRE)
             * =========================
             */
            if ($item->isDir()) {

                        $mtime = $item->getMTime();
                        if ($mtime <= 0) {
                            $modified = '1970-01-01 00:00:00'; // valor mínimo válido
                        } else {
                            $modified = date('Y-m-d H:i:s', $mtime);
                        }

                        Archivo::create([
                            'nombre'    => $item->getFilename(),
                            'ruta'      => $path,
                            'tipo'      => 'carpeta',
                            'extension' => null,
                            'size'      => 0,
                            'modified'  => $modified,
                        ]);




                continue; // ⛔ NUNCA dejar pasar a archivos
            }

            /**
             * =========================
             * 📄 ARCHIVOS
             * =========================
             */
            if (!$item->isFile()) {
                continue;
            }

            if (Archivo::where('ruta', $path)->exists()) {
                continue;
            }

            Archivo::create([
                'nombre'    => $item->getFilename(),
                'ruta'      => $path,
                'tipo'      => 'archivo',
                'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)) ?: null,
                'size'      => $item->getSize(),
                'modified'  => date('Y-m-d H:i:s', $item->getMTime()),
            ]);

            $count++;
        }

        $this->info("✅ Indexación finalizada. Archivos nuevos: {$count}");
    }
}
