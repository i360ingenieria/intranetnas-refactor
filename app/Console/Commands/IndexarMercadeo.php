<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FichaTec;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;

class IndexarMercadeo extends Command
{
    protected $signature = 'app:indexar-mercadeo {path=/mnt/nas_pcmercadeo}';
    protected $description = 'Indexa carpetas y archivos desde NAS';
    
    public function handle()
    {
        set_time_limit(3600);           // 1 hora de ejecución
        ini_set('memory_limit', '2048M'); // 2GB de RAM
        ini_set('upload_max_filesize', '2048M');
        ini_set('post_max_size', '2048M');
        
        $this->info('🔄 Indexando NAS Mercadeo...');
        $this->info('⏱️ Tiempo límite: 3600 segundos');
        $this->info('💾 Memoria límite: 2048 MB');
        $basePath = $this->argument('path');
        $count = 0;
         $this->info("📂 Indexando mercadeo: {$basePath}");

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $basePath,
                FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {

            $realPath = $item->getPathname(); // NO getRealPath (CIFS bug)
            if (!$realPath) continue;

            $path = utf8_encode($realPath);
            $nombre = $item->getFilename();

            /**
             * 🚫 EXCLUIR ARCHIVOS BASURA / TEMPORALES
             */
           if (
                $nombre === 'Thumbs.db' ||
                str_starts_with($nombre, '~$') ||
                str_starts_with($nombre, '.~lock.') ||
                str_starts_with($nombre, '.tmp.') ||
                str_starts_with($nombre, '.') ||
                str_ends_with(strtolower($nombre), '.lnk')
            ) {
                continue;
             e;
            }

            /**
             * =========================
             * 📁 CARPETAS
             * =========================
             */
            if ($item->isDir()) {

                if (!FichaTec::where('ruta', $path)->exists()) {
                    FichaTec::create([
                        'nombre'    => $nombre,
                        'ruta'      => $path,
                        'tipo'      => 'carpeta',
                        'extension' => null,
                        'size'      => 0,
                        'modified'  => date('Y-m-d H:i:s', $item->getMTime()),
                    ]);
                }

                continue;
            }

            /**
             * =========================
             * 📄 ARCHIVOS
             * =========================
             */
            if (!$item->isFile()) {
                continue;
            }

            if (FichaTec::where('ruta', $path)->exists()) {
                continue;
            }

            FichaTec::create([
                'nombre'    => $nombre,
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