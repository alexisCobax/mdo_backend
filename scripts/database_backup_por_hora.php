<?php

// Configuración
$host     = '147.182.139.71';
$usuario  = 'jkkxjmpypf';
$password = 'CtZxUaBfS8';
$database = 'jkkxjmpypf';

// Directorio de backups
$directorioBackup = __DIR__ . '/../backupDatabase/backupPorHora';

$fecha = date('Y-m-d_H-i-s');
$nombreSql = "backup_{$database}_{$fecha}.sql";
$nombreZip = "backup_{$database}_{$fecha}.zip";
$rutaSql = "{$directorioBackup}/{$nombreSql}";
$rutaZip = "{$directorioBackup}/{$nombreZip}";

// Conexión
$mysqli = new mysqli($host, $usuario, $password, $database);
if ($mysqli->connect_error) {
    die("❌ Error de conexión: " . $mysqli->connect_error);
}

// ✅ Establecer conjunto de caracteres UTF-8
$mysqli->set_charset('utf8mb4');

// Obtener tablas (solo tablas reales, no vistas)
$tablas = [];
$resultTablas = $mysqli->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'");
while ($fila = $resultTablas->fetch_array()) {
    $tablas[] = $fila[0];
}

// Obtener vistas (opcional, para que también exporte las vistas como estructura)
$vistas = [];
$resultVistas = $mysqli->query("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
while ($fila = $resultVistas->fetch_array()) {
    $vistas[] = $fila[0];
}

// Iniciar contenido SQL
$estructuraSql = "-- Backup estructura de {$database} - {$fecha}\n";
$estructuraSql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Parte 1: estructura de tablas
foreach ($tablas as $tabla) {
    $res = $mysqli->query("SHOW CREATE TABLE `$tabla`");
    $row = $res->fetch_assoc();
    $createTableSql = array_values($row)[1];
    $estructuraSql .= $createTableSql . ";\n\n";
}

// Parte 1.5: estructura de vistas
foreach ($vistas as $vista) {
    $res = $mysqli->query("SHOW CREATE VIEW `$vista`");
    $row = $res->fetch_assoc();
    $createViewSql = $row['Create View'];
    $estructuraSql .= $createViewSql . ";\n\n";
}

$estructuraSql .= "SET FOREIGN_KEY_CHECKS=1;\n\n";

// Parte 2: datos (solo de tablas reales, no vistas)
$datosSql = "-- Datos de {$database} - {$fecha}\n";

foreach ($tablas as $tabla) {
    $res = $mysqli->query("SELECT * FROM `$tabla`");
    if ($res->num_rows > 0) {
        while ($fila = $res->fetch_assoc()) {
            $valores = array_map(function ($v) use ($mysqli) {
                return is_null($v) ? 'NULL' : "'" . $mysqli->real_escape_string($v) . "'";
            }, array_values($fila));
            $datosSql .= "INSERT INTO `$tabla` VALUES (" . implode(",", $valores) . ");\n";
        }
        $datosSql .= "\n";
    }
}

// Guardar archivo SQL
file_put_contents($rutaSql, $estructuraSql . $datosSql);

// Comprimir
$zip = new ZipArchive();
if ($zip->open($rutaZip, ZipArchive::CREATE) === TRUE) {
    $zip->addFile($rutaSql, $nombreSql);
    $zip->close();
    unlink($rutaSql);
    echo "✅ Backup completo generado y comprimido: $nombreZip\n";
} else {
    echo "❌ Error al crear el archivo ZIP.\n";
}

// Limpiar si hay más de 24 backups
$archivosBackup = glob($directorioBackup . "/backup_{$database}_*.zip");
if (count($archivosBackup) > 24) {
    usort($archivosBackup, fn($a, $b) => filemtime($a) - filemtime($b));
    $archivoAEliminar = array_shift($archivosBackup);
    unlink($archivoAEliminar);
    echo "🗑️ Backup más antiguo eliminado: $archivoAEliminar\n";
}
