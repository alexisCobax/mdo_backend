<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DescargarImagenes extends Command
{
    protected $signature = 'imagenes:descargar';
    protected $description = 'Descarga imágenes de productos y las marca como descargadas';

    public function handle()
    {
        $imagenes = DB::table('fotoproducto')
            ->select('id', 'url')
            ->whereNotNull('url')
            ->where('descargada', 1) // Asumo que 1 = no descargada (ajustar si es al revés)
            ->limit(100)
            ->get();

        if ($imagenes->isEmpty()) {
            $this->info('No hay imágenes para descargar.');
            return 0;
        }

        foreach ($imagenes as $imagen) {
            //buscar y controlar si imagen se descargo marcar como descargada sino ejecutar lo siguiente
            $this->descargarImagen($imagen->url, $imagen->id);
        }

        return 0;
    }

    protected function descargarImagen($url, $id)
    {
        $filename = basename($url);
        $path = storage_path('app/public/images/' . $filename);
        $logfileError = storage_path("logs/descarga_errores.log");
        $logfileExito = storage_path("logs/descarga_exitos.log");

        // Asegurar directorio de destino
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    
        // Verificar si el archivo ya existe
        if (file_exists($path)) {
            // Solo marcar como descargado sin volver a descargar
            $this->marcarComoDescargada($id, $filename);
            $this->info($id . ": Imagen ya existente, marcada como descargada.");
    
            //Log de archivo ya existente
            $logEntry = date('Y-m-d H:i:s') . " - Imagen ID: $id ya existente, no se descargó de nuevo.\n";
            $logEntry .= "URL: $url\nArchivo: $filename\n\n";
            file_put_contents($logfileExito, $logEntry, FILE_APPEND);
            return;
        }
    
        // Ejecutar descarga
        $command = "wget --no-check-certificate \"$url\" -O \"$path\" 2>&1";
        exec($command, $output, $returnCode);
    
        if ($returnCode !== 0) {
            $logEntry = date('Y-m-d H:i:s') . " - Error al descargar $url\n";
            $logEntry .= implode("\n", $output) . "\n\n";
            file_put_contents($logfileError, $logEntry, FILE_APPEND);
            $this->error("Error al descargar la imagen ID $id");
        } else {
            $this->marcarComoDescargada($id, $filename);
            $this->info($id . ": Imagen descargada y marcada (ID: $id)");
    
            //Log de descarga exitosa
            $logEntry = date('Y-m-d H:i:s') . " - Imagen ID: $id descargada exitosamente\n";
            $logEntry .= "URL: $url\nArchivo: $filename\n\n";
            file_put_contents($logfileExito, $logEntry, FILE_APPEND);
        }
    }
    

    protected function marcarComoDescargada($id, $filename)
    {
        DB::table('fotoproducto')
            ->where('id', $id)
            ->update(['descargada' => 2, 'nombre' => $filename]); // Asumo 2 = descargada
    }
}
