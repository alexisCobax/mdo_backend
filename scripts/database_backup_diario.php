<?php

// Configuración
$host     = '147.182.139.71';
$usuario  = 'jkkxjmpypf';
$password = 'CtZxUaBfS8';
$database = 'jkkxjmpypf';

// Directorio de backups diarios
$directorioBackup = __DIR__ . '/../backupDatabase/backupDiario';

// Asegurar que exista el directorio
if (!file_exists($directorioBackup)) {
    mkdir($directorioBackup, 0777, true);
}

// Fecha actual para nombrar el archivo
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
$mysqli->set_charset('utf8mb4');

// Obtener tablas reales
$tablas = [];
$resultTablas = $mysqli->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'");
while ($fila = $resultTablas->fetch_array()) {
    $tablas[] = $fila[0];
}

// Obtener vistas
$vistas = [];
$resultVistas = $mysqli->query("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
while ($fila = $resultVistas->fetch_array()) {
    $vistas[] = $fila[0];
}

// Iniciar contenido SQL
$estructuraSql = "-- Backup estructura de {$database} - {$fecha}\n";
$estructuraSql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Estructura de tablas
foreach ($tablas as $tabla) {
    $res = $mysqli->query("SHOW CREATE TABLE `$tabla`");
    $row = $res->fetch_assoc();
    $estructuraSql .= $row['Create Table'] . ";\n\n";
}

// Estructura de vistas (sin datos)
foreach ($vistas as $vista) {
    $res = $mysqli->query("SHOW CREATE VIEW `$vista`");
    $row = $res->fetch_assoc();
    $estructuraSql .= $row['Create View'] . ";\n\n";
}

$estructuraSql .= "SET FOREIGN_KEY_CHECKS=1;\n\n";

// Exportar datos de tablas
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

// Guardar SQL en archivo
file_put_contents($rutaSql, $estructuraSql . $datosSql);

// Comprimir el SQL en ZIP
$zip = new ZipArchive();
if ($zip->open($rutaZip, ZipArchive::CREATE) === TRUE) {
    $zip->addFile($rutaSql, $nombreSql);
    $zip->close();
    unlink($rutaSql);
    echo "✅ Backup diario generado y comprimido: $nombreZip\n";
} else {
    echo "❌ Error al crear el archivo ZIP.\n";
}
