<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\Log;

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
        $this->runStep('Limpiando cache...', function () {
            $this->callArtisanSafe('cache:clear');
            $this->callArtisanSafe('config:clear');
            $this->callArtisanSafe('view:clear');
        });

        // 2. Limpiar base de datos
        $this->runStep('Limpiando base de datos...', function () {
            $this->callArtisanSafe('db:wipe', ['--force' => true]);
        });

        // 3. Ejecutar migraciones
        $this->runStep('Ejecutando migraciones...', function () {
            $this->callArtisanSafe('migrate', ['--force' => true]);
        });

        // 4. Limpiar storage (excepto si se usa la opción --no-storage)
        if (!$this->option('no-storage')) {
            $this->runStep('Limpiando storage...', function () {
                $this->cleanStorage();
            });
        }

        // 5. Ejecutar seeders (excepto si se usa la opción --no-seed)
        if (!$this->option('no-seed')) {
            $this->runStep('Ejecutando seeders...', function () {
                $this->callArtisanSafe('db:seed', ['--force' => true]);
            });
        }

        $this->info('¡Reset de aplicación completado! 🎉');
    }

    /**
     * Ejecuta un paso y atrapa excepciones mostrando archivo y linea.
     */
    protected function runStep(string $message, callable $callback)
    {
        $this->info($message);

        try {
            $callback();
        } catch (Throwable $e) {
            $err = sprintf(
                "Excepción: %s en %s:%d\nTrace:\n%s",
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );

            // Mostrar en consola
            $this->error($err);

            // Guardar en log (storage/logs/laravel.log)
            Log::error($err);

            // Abortar el comando con código de error
            return $this->fail("Paso falló — revisa la traza anterior o laravel.log para más detalles.");
        }
    }

    /**
     * Llama a Artisan::call y reporta salida/código si hay error.
     */
    protected function callArtisanSafe(string $command, array $params = [])
    {
        // Merge params and preparar
        $params = array_merge($params, ['command' => $command]);
        $exitCode = Artisan::call($command, $params);

        // Obtener salida textual
        $output = Artisan::output();

        if ($exitCode !== 0) {
            $msg = "Comando Artisan \"$command\" falló con exitCode={$exitCode}. Output:\n" . $output;
            $this->error($msg);
            Log::error($msg);
            // Lanzar excepción para que runStep la capture y muestre archivo/linea
            throw new \RuntimeException($msg);
        }

        // Opcional: show output when verbose
        if ($this->getOutput()->getVerbosity() >= 2) {
            $this->line("Output de $command: " . trim($output));
        }
    }

    protected function cleanStorage()
    {
        // Directorios a limpiar en storage/app/public
        $directoriesToClean = [
            'solicitudes',
            'profiles',
            'temp',
            'uploads',
            'xml',
            'pdf'
        ];

        $disk = Storage::disk('public');

        foreach ($directoriesToClean as $directory) {
            try {
                if ($disk->exists($directory)) {
                    $disk->deleteDirectory($directory);
                    $this->line("Directorio {$directory} eliminado");
                } else {
                    $this->line("Directorio {$directory} no existe (omitido).");
                }
            } catch (Throwable $e) {
                $msg = sprintf("Error al eliminar %s: %s en %s:%d", $directory, $e->getMessage(), $e->getFile(), $e->getLine());
                $this->error($msg);
                Log::error($msg . "\n" . $e->getTraceAsString());
                throw $e; // relanzar para que runStep lo capture
            }
        }

        // Mantener el directorio .gitignore
        try {
            if (!$disk->exists('.gitignore')) {
                $disk->put('.gitignore', "*\n!.gitignore\n");
            }
        } catch (Throwable $e) {
            $msg = sprintf("Error al escribir .gitignore: %s en %s:%d", $e->getMessage(), $e->getFile(), $e->getLine());
            $this->error($msg);
            Log::error($msg . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }
}
