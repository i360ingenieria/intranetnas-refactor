<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archivo;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;

class IndexarNasx extends Command
{
    protected $signature = 'app:indexar-nasx {path=/mnt/intranet}';

    protected $description = 'Indexa carpetas y archivos desde NAS';

    public function handle()
    {
          set_time_limit(3600);           // 1 hora de ejecución
        ini_set('memory_limit', '2048M'); // 2GB de RAM
        ini_set('upload_max_filesize', '2048M');
        ini_set('post_max_size', '2048M');
        
        $this->info('🔄 Indexando NAS Intranet...');
        $this->info('⏱️ Tiempo límite: 3600 segundos');
        $this->info('💾 Memoria límite: 2048 MB');
        $basePath = $this->argument('path');

        $countNew = 0;
        $countUpdated = 0;

        $this->info("📂 Indexación incremental NAS: {$basePath}");

        // Cache de archivos existentes
        $existentes = Archivo::select('ruta', 'modified', 'size')
            ->get()
            ->keyBy('ruta');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $basePath,
                FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {

            try {
                $path = $item->getPathname();
                if (!$path) continue;

                // 📌 Nombre del archivo/carpeta
             $nombre = $item->getFilename();

                /**
                 * 🚫 EXCLUIR OCULTOS Y BASURA
                 */
                if (
                    str_starts_with($nombre, '.') ||          // ocultos (.tmp, .git, .cache...)
                    $nombre === 'Thumbs.db' ||                // Windows
                    str_starts_with($nombre, '~$') ||        // Excel temporal
                    str_ends_with(strtolower($nombre), '.lnk') // accesos directos
                ) {
                    continue;
                }
                                                $mtime = $item->getMTime();
                $modified = $mtime > 0
                    ? date('Y-m-d H:i:s', $mtime)
                    : '1970-01-01 00:00:00';

                /**
                 * =========================
                 * 📁 CARPETAS
                 * =========================
                 */
                if ($item->isDir()) {

                    if (!isset($existentes[$path])) {

                        Archivo::create([
                            'nombre'    => $nombre,
                            'ruta'      => $path,
                            'tipo'      => 'carpeta',
                            'extension' => null,
                            'size'      => 0,
                            'modified'  => $modified,
                        ]);

                        $countNew++;
                    }

                    continue;
                }

                /**
                 * =========================
                 * 📄 ARCHIVOS
                 * =========================
                 */
                if (!$item->isFile()) continue;

                $size = $item->getSize();

                // NUEVO
                if (!isset($existentes[$path])) {

                    Archivo::create([
                        'nombre'    => $nombre,
                        'ruta'      => $path,
                        'tipo'      => 'archivo',
                        'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)) ?: null,
                        'size'      => $size,
                        'modified'  => $modified,
                    ]);

                    $countNew++;
                    continue;
                }

                // EXISTE → validar cambios
                $db = $existentes[$path];

                if ($db->modified !== $modified || $db->size != $size) {

                    Archivo::where('ruta', $path)->update([
                        'nombre'   => $nombre,
                        'size'     => $size,
                        'modified' => $modified,
                    ]);

                    $countUpdated++;
                }

            } catch (\Throwable $e) {
                $this->warn("Error en: {$item->getPathname()}");
            }
        }

        $this->info("✅ Nuevos: {$countNew}");
        $this->info("🔄 Actualizados: {$countUpdated}");
    }
}