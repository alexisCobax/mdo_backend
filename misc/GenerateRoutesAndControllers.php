<?php

$directorio =  __DIR__.'/app/Models/'; // Reemplaza con la ruta de la carpeta que deseas listar

// Obtener la lista de archivos en el directorio
$archivos = scandir($directorio);

// Recorrer los archivos y mostrarlos en pantalla
foreach ($archivos as $archivo) {
    if ($archivo != '.' && $archivo != '..') {
        shell_exec('php artisan make:controller '.basename($archivo, '.php').'Controller --resource --model='.basename($archivo, '.php').'');
    }
}

?>
