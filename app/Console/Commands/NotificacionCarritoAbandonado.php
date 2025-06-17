<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PDO;

class NotificacionCarritoAbandonado extends Command
{
    protected $signature = 'notificar:abandonados {estado}';
    protected $description = 'Notifica carritos abandonados y actualiza su estado';

    public function handle()
    {
        Log::info('Comando notificar:abandonados iniciado.');

        // Obtener el argumento 'estado' desde la consola
        $estado = $this->argument('estado');

        // Obtener conexión PDO desde Laravel
        $pdo = app('db')->getPdo();

        $SQL = "SELECT
                carrito.id AS carrito_id,
                cliente.id AS cliente_id,
                carrito.*,
                cliente.*
                FROM carrito
                LEFT JOIN
                cliente
                ON
                carrito.cliente=cliente.id
                WHERE fecha
                BETWEEN DATE_SUB(CURDATE(), INTERVAL 5 DAY)
                AND DATE_SUB(CURDATE(), INTERVAL 5 DAY) + INTERVAL 1 DAY - INTERVAL 1 SECOND
                AND estado = 0
                AND notificacion = 0;";

        // Consulta para obtener carritos abandonados
        $stmt = $pdo->prepare($SQL);
        $stmt->execute();
        $carritos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (empty($carritos)) {
            Log::info('No hay carritos abandonados para notificar.');
            $this->info('No hay carritos abandonados para notificar.');
            return;
        }

        // URL de la API
        $apiUrl = 'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/aa90d2ed-c919-46a4-a9b5-0a9134a0304a';

        foreach ($carritos as $carrito) {
            // Datos a enviar a la API
            $data = [
                'estado' =>  $estado,
                'emailTest' => $carrito->email
            ];

            // Enviar datos a la API con cURL
            $response = $this->enviarNotificacion($apiUrl, $data);

            //Si la API responde correctamente, actualizar la tabla con PDO
            if ($response['success']) {
                $updateStmt = $pdo->prepare("UPDATE carrito SET notificacion = 1 WHERE id = ?");
                $updateStmt->execute([$carrito->carrito_id]);

                Log::info("Carrito ID {$carrito->carrito_id} notificado y actualizado.");
                $this->info("Carrito ID {$carrito->carrito_id} notificado y actualizado.");
            } else {
                Log::error("Error al notificar carrito ID {$carrito->carrito_id}: " . $response['error']);
                $this->error("Error al notificar carrito ID {$carrito->carrito_id}: " . $response['error']);
            }
        }

        Log::info('Comando notificar:abandonados finalizado.');
    }

    private function enviarNotificacion($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($httpCode == 200) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $error ?: "Código HTTP: $httpCode"];
        }
    }
}
