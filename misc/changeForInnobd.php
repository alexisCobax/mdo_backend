<?php

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the Laravel application
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Obtener todas las tablas de la base de datos
$tables = DB::select('SHOW TABLES');
$tables = array_map('current', $tables);

// Cambiar el motor de almacenamiento a InnoDB para cada tabla
foreach ($tables as $table) {
    Schema::connection('mysql')->table($table, function ($table) {
        $table->engine = 'InnoDB';
    });
}

// Guardar los cambios
DB::commit();
