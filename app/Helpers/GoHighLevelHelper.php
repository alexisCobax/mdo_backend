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
                    //'refresh_token' => $configuraciones['ghl_refresh_token'],
                    'refresh_token' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoQ2xhc3MiOiJMb2NhdGlvbiIsImF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJzb3VyY2UiOiJJTlRFR1JBVElPTiIsInNvdXJjZUlkIjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5LW0xcXo1bHFxIiwiY2hhbm5lbCI6Ik9BVVRIIiwicHJpbWFyeUF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJvYXV0aE1ldGEiOnsic2NvcGVzIjpbImNvbnRhY3RzLnJlYWRvbmx5IiwiY29udGFjdHMud3JpdGUiXSwiY2xpZW50IjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5IiwidmVyc2lvbklkIjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5IiwiY2xpZW50S2V5IjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5LW0xcXo1bHFxIn0sImlhdCI6MTc1NDUwOTAzOS44NDUsImV4cCI6MTc4NjA0NTAzOS44NDUsInVuaXF1ZUlkIjoiZTY3ODdjZGItMmUzYi00OTI5LWExNTYtMjliZmM2MjkwOTE3IiwidiI6IjIifQ.H2U3nje7Hs2rjbpsw19sk6XBeXdAQF7iaLqFbgSrdxO5_nneWu4tmrOp5vFe61jApaiKDs7X39yq60-CaClXClFBVgQWmIWKn1gpOCxEARWU3ryFJVqrxOstHY9w2B4sWxjKulNM4cq4I6wPXk3Ajan-yb0QvI5DtlWd7sbifaI693Hk-m-NsJ9kenSc6ijVi6zMegTfyMS5xfHWXyRv0Xz-1Wcn7G1lRwFLF8fxJbITDOLRvmO2m_Ly_lrsBz58Iuz3FVLtoDQPBzJ4grXQ57JXGmWtOSRePKQ8Wnt2wsn9rtXCLh8o9J0VMjIhr13XwjG8TbO5G7lR06mQkUmugQ5LCRoF7DH1UV659mzuuyIZaMh_H1k1rxma3Rn2rk7OaJTh94XrvceXwdwIxdIbGef7Y-OoO6L5GosK3q46dxVMMTGKspoNHj-5N0RQCxocBKFgPk6IBhyr07G0Pa8rq2DcOKXAvXVbAeZsfDHLtg8uwBcLoD6ON1e_mrL4CnBWuckJ3OQAoorhpagi6aoYpOWd-79SaYByGol3niyjmi4J80hT91BbADhSvRivcgL1cS5n1b7r1cPZe2e246IH-AupPPtPQRWQCRWOPsJn2QYOsuXI_yA_y5Wzs4d6yzjIPiIy5aenIh5PCTfe33FeHRgrPAEjrAYY82JRFd-XyWw',
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
