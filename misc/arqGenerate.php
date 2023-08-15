<?php

// Cargar la aplicación Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Obtener la lista de modelos
$modelsPath = app_path('Models');
$models = [];
$files = scandir($modelsPath);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $model = pathinfo($file, PATHINFO_FILENAME);
        $models[] = $model;
    }
}

// Ejecutar el comando generate:service para cada modelo
foreach ($models as $model) {
    $command = "generate:service {$model}";
    \Artisan::call($command);
    $output = \Artisan::output();
    echo "{$model}: {$output}\n";
}
