<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ResetApplication extends Command
{
    protected $signature = 'app:reset 
                          {--no-seed : No ejecutar los seeders}
                          {--no-storage : No limpiar el storage}';
    
    protected $description = 'Reinicia la aplicación: wipe DB, migrate, seed y limpia storage';

    public function handle()
    {
        // Confirmar en producción
        if (app()->isProduction()) {
            if (!$this->confirm('¿Estás seguro de que quieres resetear la aplicación en producción?')) {
                $this->info('Operación cancelada.');
                return;
            }
        }

        $this->info('Iniciando reset de la aplicación...');

        // 1. Limpiar cache
        $this->info('Limpiando cache...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        // 2. Limpiar base de datos
        $this->info('Limpiando base de datos...');
        Artisan::call('db:wipe', ['--force' => true]);

        // 3. Ejecutar migraciones
        $this->info('Ejecutando migraciones...');
        Artisan::call('migrate', ['--force' => true]);

        // 4. Limpiar storage (excepto si se usa la opción --no-storage)
        if (!$this->option('no-storage')) {
            $this->info('Limpiando storage...');
            $this->cleanStorage();
        }

        // 5. Ejecutar seeders (excepto si se usa la opción --no-seed)
        if (!$this->option('no-seed')) {
            $this->info('Ejecutando seeders...');
            Artisan::call('db:seed', ['--force' => true]);
        }

        $this->info('¡Reset de aplicación completado! 🎉');
    }

    protected function cleanStorage()
    {
        // Directorios a limpiar en storage/app/public
        $directoriesToClean = [
            'solicitudes',
            'profiles',
            'temp',
            'uploads'
        ];

        $disk = Storage::disk('public');

        foreach ($directoriesToClean as $directory) {
            if ($disk->exists($directory)) {
                $disk->deleteDirectory($directory);
                $this->line("Directorio {$directory} eliminado");
            }
        }

        // Mantener el directorio .gitignore
        if (!$disk->exists('.gitignore')) {
            $disk->put('.gitignore', "*\n!.gitignore\n");
        }
    }
}