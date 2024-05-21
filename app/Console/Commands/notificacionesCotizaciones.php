<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificacionesCotizacionService;

class notificacionesCotizaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:notificaciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera notificaciones';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Llama al método del controlador que maneja el endpoint
        $controlador = new NotificacionesCotizacionService();
        $resultado = $controlador->cotizacion();

        // Maneja el resultado según sea necesario
        $this->info('El comando se ejecutó correctamente');
        $this->info('Resultado: ' . $resultado);
    }
}
