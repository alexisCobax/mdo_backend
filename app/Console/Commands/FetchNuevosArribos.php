<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GoHighLevelController;
use Illuminate\Support\Facades\Log;

class FetchNuevosArribos extends Command
{
    /**
     * El nombre del comando para usar en Artisan
     */
    protected $signature = 'gohighlevel:enviar-nuevos-arribos';

    /**
     * Descripción del comando
     */
    protected $description = 'Envía el template de nuevos arribos a GoHighLevel';

    /**
     * Ejecuta el comando
     */
    public function handle()
    {
    $controller = new GoHighLevelController();
    $response = $controller->enviarNuevosArribos();
    $data = $response->getData();

    $this->info("Ejecución finalizada. Respuesta:");
    $this->line(json_encode($data, JSON_PRETTY_PRINT));

    // Guardar en log separado
    Log::build([
        'driver' => 'single',
        'path' => storage_path('logs/nuevos_arribos.log'),
    ])->info('Ejecución de gohighlevel:enviar-nuevos-arribos', [
        'fecha' => now()->toDateTimeString(),
        'respuesta' => $data
    ]);

    return 0;
        // $controller = new GoHighLevelController();
        // $response = $controller->enviarNuevosArribos();

        // $this->info("Ejecución finalizada. Respuesta:");
        // $this->line(json_encode($response->getData(), JSON_PRETTY_PRINT));

        // return 0;
    }
}
