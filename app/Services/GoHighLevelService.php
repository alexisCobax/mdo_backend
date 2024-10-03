<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Response;
use App\Helpers\GoHighLevelHelper;
use Illuminate\Support\Facades\Log;
use App\Models\ConfiguracionesGenerales;

class GoHighLevelService
{

    public static function createContact($payload)
    {
        try {
            $token = GoHighLevelHelper::getToken();
            if (!$token || !isset($token['access_token'])) {
                throw new Exception('Error al obtener el token de acceso.');
            }

            $locationId = ConfiguracionesGenerales::where('nombre', 'ghl_location_id')->first();
            if (!$locationId || !$locationId->valor) {
                throw new Exception('Error al obtener la locationId.');
            }

            $payload['locationId'] = $locationId->valor;

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://services.leadconnectorhq.com/contacts/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Authorization: Bearer ' . $token['access_token'],
                    'Content-Type: application/json',
                    'Version: 2021-07-28'
                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                $curlError = curl_error($curl);
                curl_close($curl);
                throw new Exception('Error en la solicitud cURL: ' . $curlError);
            }

            $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpStatusCode < 200 || $httpStatusCode >= 300) {
                throw new Exception('Error en la respuesta HTTP: Código ' . $httpStatusCode . ' - Respuesta: ' . $response);
            }

            $decodedResponse = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar la respuesta JSON: ' . json_last_error_msg());
            }

            return $decodedResponse;
        } catch (Exception $e) {
            Log::error('Error al crear el contacto en GHL: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}
