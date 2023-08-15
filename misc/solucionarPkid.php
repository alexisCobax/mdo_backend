<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require_once dirname(__DIR__).'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Obtener el listado de tablas
$tables = DB::select('SHOW TABLES');

foreach ($tables as $table) {
    $tableName = $table->{'Tables_in_' . env('DB_DATABASE')};

    // Verificar si la tabla tiene una clave primaria definida
    $primaryKey = Schema::getColumnListing($tableName);
    if (count($primaryKey) > 0) {
        echo "La tabla '$tableName' ya tiene una clave primaria definida. Se omitirá.\n";
        //continue;
    }

    // Verificar si la columna 'id' ya existe en la tabla actual
    $columnExists = Schema::hasColumn($tableName, 'id');
    if ($columnExists) {
        echo "La columna 'id' ya existe en la tabla '$tableName'. Se omitirá.\n";
        //continue;
    }

    // Verificar si la columna 'tipoUbicacion' ya existe en la tabla actual
    $tipoUbicacionExists = Schema::hasColumn($tableName, 'tipoUbicacion');
    if ($tipoUbicacionExists) {
        echo "La columna 'tipoUbicacion' ya existe en la tabla '$tableName'. Se omitirá.\n";
        //continue;
    }

    // Obtener los datos existentes de la tabla
    $existingData = DB::table($tableName)->get();

    // Crear una nueva tabla con la estructura deseada
    Schema::create($tableName . '_new', function ($table) {
        $table->increments('id');
        $table->integer('tipoUbicacion');
        // Agregar aquí las columnas adicionales de la tabla existente
    });

    // Migrar los datos de la tabla existente a la nueva tabla
    foreach ($existingData as $data) {
        unset($data->id); // Eliminar el campo 'id' existente
        DB::table($tableName . '_new')->insert((array)$data);
    }

    // Eliminar la tabla existente
    Schema::dropIfExists($tableName);

    // Renombrar la nueva tabla para que tenga el nombre original
    Schema::rename($tableName . '_new', $tableName);

    echo "La tabla '$tableName' se modificó exitosamente.\n";
}





