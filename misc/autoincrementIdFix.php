<?php

function renombrar($archivo){

$contenido = file_get_contents($archivo);

// Buscar y reemplazar la línea $table->integer('id', true);
$nuevoContenido = str_replace('$table->integer(\'id\', true);', '$table->integer(\'id\')->autoIncrement();', $contenido);

// Buscar y reemplazar la línea $table->integer('id');
$nuevoContenido = str_replace('$table->integer(\'id\');', '$table->integer(\'id\')->autoIncrement();', $nuevoContenido);

// Guardar los cambios en el archivo
file_put_contents($archivo, $nuevoContenido);

echo "El archivo se ha modificado correctamente.";

}


$directorio = './database/migrations/'; // Reemplaza con la ruta de la carpeta que deseas listar

// Obtener la lista de archivos en el directorio
$archivos = scandir($directorio);
$i=0;
// Iterar sobre los archivos y mostrarlos en pantalla
foreach ($archivos as $archivo) {
    // Excluir los directorios "." y ".."
    if ($archivo != '.' && $archivo != '..') {
        renombrar($directorio.$archivo);
    }
$i++;
}

echo $i;
