<?php

// Configuración
$host     = '147.182.139.71';
$usuario  = 'jkkxjmpypf';
$password = 'CtZxUaBfS8';
$database = 'jkkxjmpypf';

// Generar nombres con fecha y hora
$fecha = date('Y-m-d_H-i-s');
$nombreSql = "backup_{$database}_{$fecha}.sql";
$nombreZip = "backup_{$database}_{$fecha}.zip";

$rutaSql = __DIR__ . '/' . $nombreSql;
$rutaZip = __DIR__ . '/' . $nombreZip;

// Ejecutar mysqldump
$comando = "mysqldump -h {$host} -u {$usuario} -p{$password} {$database} > {$rutaSql}";
exec($comando, $output, $result);

// Verificar resultado del dump
if ($result !== 0) {
    echo "❌ Error al generar el backup SQL. Código: $result\n";
    exit(1);
}

// Limpiar SEQUENCE del archivo .sql
$contenido = file_get_contents($rutaSql);
if ($contenido === false) {
    echo "❌ No se pudo leer el archivo de respaldo.\n";
    exit(1);
}

// Eliminar líneas que contienen 'SEQUENCE'
$contenidoLimpio = preg_replace('/^.*SEQUENCE.*\n?/mi', '', $contenido);

// Sobrescribir el archivo original con el contenido limpio
file_put_contents($rutaSql, $contenidoLimpio);

// Comprimir en ZIP
$zip = new ZipArchive();
if ($zip->open($rutaZip, ZipArchive::CREATE) === TRUE) {
    $zip->addFile($rutaSql, $nombreSql);
    $zip->close();

    // Eliminar el .sql original después de comprimir
    unlink($rutaSql);

    echo "✅ Backup limpio y comprimido correctamente: $nombreZip\n";
} else {
    echo "❌ Error al crear el archivo ZIP.\n";
}
