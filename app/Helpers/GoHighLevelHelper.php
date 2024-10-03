<?php

namespace App\Helpers;

use App\Models\ConfiguracionesGenerales;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class GoHighLevelHelper
{

    static function getToken()
    {
        try {
            $campos = [
                'ghl_client_id',
                'ghl_client_secret',
                'ghl_grant_type',
                'ghl_location',
                'ghl_refresh_token',
                'ghl_user_type'
            ];

            $configuraciones = ConfiguracionesGenerales::whereIn('nombre', $campos)->pluck('valor', 'nombre');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query(array(
                    'client_id' => $configuraciones['ghl_client_id'],
                    'client_secret' => $configuraciones['ghl_client_secret'],
                    'grant_type' => $configuraciones['ghl_grant_type'],
                    'code' => '',
                    'refresh_token' => $configuraciones['ghl_refresh_token'],
                    'user_type' => $configuraciones['ghl_user_type'],
                    'redirect_uri' => ''
                )),
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            if ($response === false) {
                Log::error('Error en la solicitud cURL: ' . curl_error($curl));
                return null;
            }

            $response = json_decode($response, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $registro = ConfiguracionesGenerales::where('nombre', 'ghl_refresh_token')->first();
                $registro->valor = $response['refresh_token'];
                $registro->save();

                return $response;
            } else {
                Log::error('Error al decodificar la respuesta JSON GHL: ' . json_last_error_msg());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Ocurrió un error al obtener datos de la api GHL: ' . $e->getMessage());
            return null;
        }
    }
}
