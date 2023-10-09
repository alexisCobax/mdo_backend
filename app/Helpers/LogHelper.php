<?php

namespace App\Helpers;

class LogHelper
{
    public static function get($data)
    {
        $filePath = storage_path('app/public/log.txt'); // Ruta al archivo TXT dentro de la carpeta de almacenamiento
        $file = fopen($filePath, 'a');
        fwrite($file, date('Y-m-d H:i:s') . ' ' . __FILE__ . ' (Line ' . __LINE__ . '): ' . json_encode($data) . PHP_EOL);

        fclose($file);
    }
}
