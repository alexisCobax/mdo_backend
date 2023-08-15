<?php

// Expresión regular para encontrar la función `findAll()`
$pattern = '/public function findAll\(\)\s*{[^}]*}/s';

// Carpeta donde se encuentran los archivos
$folderPath = '../app/Services';

// Recorrer los archivos en la carpeta
foreach (glob($folderPath . '/*Service.php') as $file) {
    // Obtener el nombre base del archivo sin el sufijo "Service.php"
    $fileName = basename($file, 'Service.php');

    // // Leer el contenido del archivo
    // $content = file_get_contents($file);

    // // Generar la nueva clase a partir del nombre del archivo
    // $className = ucfirst($fileName);

    // // Generar el nuevo código de la función `findAll()`
    // $replacement = 'public function findAll(Request $request)
    // {
    //     $data = PaginateHelper::getPaginatedData($request, ' . $className . '::class);

    //     return response()->json([\'data\' => $data], 200);
    // }';

    // // Realizar el reemplazo utilizando la expresión regular
    // $modifiedContent = preg_replace($pattern, $replacement, $content);

    // // Escribir el contenido modificado de vuelta al archivo
    // file_put_contents($file, $modifiedContent);

    echo "Se ha modificado el archivo: $file\n";
}

echo "Cambios masivos completados.\n";
