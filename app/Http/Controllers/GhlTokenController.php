<?php

namespace App\Http\Controllers;

use App\Helpers\GoHighLevelHelper;
use App\Models\ConfiguracionesGenerales;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Módulo aislado para obtener Bearer tokens de GoHighLevel.
 * Usa GoHighLevelHelper::getToken() (mismo mecanismo que la tienda).
 * No depende ni modifica ningún controller/servicio/helper existente.
 */
class GhlTokenController extends Controller
{
    /**
     * Obtiene un Bearer token válido para GoHighLevel.
     * Usa GoHighLevelHelper (mismo flujo que la tienda al registrar clientes).
     * Si falla, hace una llamada diagnóstica para mostrar el error real.
     *
     * GET /api/ghl/token
     */
    public function getToken()
    {
        try {
            $tokenResponse = GoHighLevelHelper::getToken();

            if ($tokenResponse && isset($tokenResponse['access_token'])) {
                return response()->json([
                    'status' => 'success',
                    'bearer_token' => $tokenResponse['access_token'],
                    'token_info' => [
                        'token_type' => $tokenResponse['token_type'] ?? 'Bearer',
                        'expires_in' => $tokenResponse['expires_in'] ?? null,
                        'scope' => $tokenResponse['scope'] ?? null,
                        'locationId' => $tokenResponse['locationId'] ?? null,
                        'userId' => $tokenResponse['userId'] ?? null,
                        'generated_at' => date('Y-m-d H:i:s'),
                    ],
                    'usage' => 'Authorization: Bearer ' . $tokenResponse['access_token'],
                ], 200);
            }

            return $this->diagnose();
        } catch (\Exception $e) {
            Log::error('GhlTokenController: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * TEMPORAL: Lee las credenciales de GHL desde la DB de producción.
     * ELIMINAR después de sincronizar.
     *
     * GET /api/ghl/prod-config
     */
    public function prodConfig()
    {
        try {
            $rows = DB::connection('production')->table('configuracionesgenerales')
                ->whereIn('nombre', [
                    'ghl_client_id', 'ghl_client_secret', 'ghl_grant_type',
                    'ghl_refresh_token', 'ghl_user_type', 'ghl_location',
                    'ghl_location_id',
                ])
                ->pluck('valor', 'nombre');

            $masked = $rows->map(function ($value, $key) {
                if (in_array($key, ['ghl_client_secret'])) {
                    return substr($value, 0, 8) . '***';
                }
                if (in_array($key, ['ghl_refresh_token'])) {
                    return substr($value, 0, 50) . '... (' . strlen($value) . ' chars)';
                }
                return $value;
            });

            return response()->json([
                'status' => 'success',
                'production_ghl_config' => $masked,
                'nota' => 'TEMPORAL - Eliminar este endpoint después de sincronizar.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * TEMPORAL: Sincroniza las credenciales de GHL de producción a la DB local.
     * ELIMINAR después de usar.
     *
     * POST /api/ghl/sync-from-prod
     */
    public function syncFromProd()
    {
        try {
            $prodRows = DB::connection('production')->table('configuracionesgenerales')
                ->whereIn('nombre', [
                    'ghl_client_id', 'ghl_client_secret', 'ghl_grant_type',
                    'ghl_refresh_token', 'ghl_user_type', 'ghl_location',
                    'ghl_location_id',
                ])
                ->pluck('valor', 'nombre');

            if ($prodRows->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontraron credenciales GHL en producción.',
                ], 404);
            }

            $updated = [];
            foreach ($prodRows as $nombre => $valor) {
                $affected = ConfiguracionesGenerales::where('nombre', $nombre)
                    ->update(['valor' => $valor]);
                if ($affected) {
                    $updated[] = $nombre;
                } else {
                    ConfiguracionesGenerales::create(['nombre' => $nombre, 'valor' => $valor, 'estado' => 1]);
                    $updated[] = $nombre . ' (creado)';
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Credenciales de GHL sincronizadas desde producción.',
                'campos_actualizados' => $updated,
                'nota' => 'Ahora probá GET /api/ghl/token',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra el estado de salud de los tokens de templates.
     * Indica si el access token y el refresh token están vigentes.
     *
     * GET /api/ghl/token-status
     */
    public function tokenStatus()
    {
        try {
            $configFile = base_path('config/gohighlevel.json');

            if (!file_exists($configFile)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No existe config/gohighlevel.json',
                ], 500);
            }

            $config = json_decode(file_get_contents($configFile), true);
            $now = time();

            $accessTokenExpiresAt = $config['token_expires_at'] ?? 0;
            $accessTokenValid = $now < ($accessTokenExpiresAt - 300);
            $accessTokenSecsLeft = max(0, $accessTokenExpiresAt - $now);

            $refreshTokenExpiresAt = $config['refresh_token_expires_at'] ?? 0;

            if ($refreshTokenExpiresAt === 0 && !empty($config['refreshToken'])) {
                $parts = explode('.', $config['refreshToken']);
                if (count($parts) === 3) {
                    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                    $refreshTokenExpiresAt = isset($payload['exp']) ? (int) $payload['exp'] : 0;
                }
            }

            $refreshTokenDaysLeft = $refreshTokenExpiresAt > 0
                ? round(($refreshTokenExpiresAt - $now) / 86400, 1)
                : null;

            $refreshTokenHealth = 'unknown';
            if ($refreshTokenDaysLeft !== null) {
                if ($refreshTokenDaysLeft <= 0) $refreshTokenHealth = 'EXPIRED';
                elseif ($refreshTokenDaysLeft < 7) $refreshTokenHealth = 'CRITICAL';
                elseif ($refreshTokenDaysLeft < 30) $refreshTokenHealth = 'WARNING';
                else $refreshTokenHealth = 'OK';
            }

            return response()->json([
                'status' => 'success',
                'access_token' => [
                    'valid' => $accessTokenValid,
                    'expires_at' => $accessTokenExpiresAt > 0 ? date('Y-m-d H:i:s', $accessTokenExpiresAt) : null,
                    'seconds_left' => $accessTokenSecsLeft,
                    'auto_refresh' => 'TokenManager renueva automáticamente al expirar',
                ],
                'refresh_token' => [
                    'health' => $refreshTokenHealth,
                    'expires_at' => $refreshTokenExpiresAt > 0 ? date('Y-m-d H:i:s', $refreshTokenExpiresAt) : null,
                    'days_left' => $refreshTokenDaysLeft,
                    'nota' => $refreshTokenHealth === 'OK'
                        ? 'Sin acción necesaria'
                        : ($refreshTokenHealth === 'EXPIRED'
                            ? 'URGENTE: Re-autorizar con GET /api/ghl/authorize?redirect_uri=http://127.0.0.1:8080/getAccessToken.php'
                            : 'Planificar re-autorización pronto'),
                ],
                'app' => [
                    'client_id' => $config['clientId'] ?? '',
                    'scope' => $config['scope'] ?? '',
                    'locationId' => $config['locationId'] ?? '',
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera la URL de autorización OAuth para la app de templates de GHL.
     *
     * GET /api/ghl/authorize
     */
    public function authorizeUrl(\Illuminate\Http\Request $request)
    {
        $configFile = base_path('config/gohighlevel.json');
        $config = json_decode(file_get_contents($configFile), true);

        $prodUrl = 'https://phpstack-1091339-3819555.cloudwaysapps.com';
        $redirectUri = $request->query('redirect_uri', $prodUrl . '/api/ghl/callback');

        $params = http_build_query([
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'client_id' => $config['clientId'],
            'scope' => $config['scope'] ?? '',
        ]);

        $authUrl = 'https://marketplace.gohighlevel.com/oauth/chooselocation?' . $params;

        return response()->json([
            'status' => 'success',
            'message' => 'Abrí esta URL en el navegador. Después de autorizar, GHL redirige con ?code=XXX. Copiá el code de la URL y usá POST /api/ghl/exchange.',
            'authorization_url' => $authUrl,
            'redirect_uri_used' => $redirectUri,
            'next_step' => 'POST /api/ghl/exchange  { "code": "EL_CODE_DE_LA_URL" }',
            'nota' => 'Si el redirect_uri no coincide, probá con ?redirect_uri=OTRA_URL.',
        ], 200);
    }

    /**
     * Callback OAuth. GHL redirige acá con ?code=XXX.
     * Intercambia el code por tokens y los guarda en gohighlevel.json.
     *
     * GET /api/ghl/callback?code=XXX
     */
    public function callback(\Illuminate\Http\Request $request)
    {
        $code = $request->query('code');

        if (empty($code)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se recibió el parámetro "code".',
                'query_params' => $request->query(),
            ], 400);
        }

        $prodUrl = 'https://phpstack-1091339-3819555.cloudwaysapps.com';
        return $this->exchangeCode($code, $prodUrl . '/api/ghl/callback');
    }

    /**
     * Intercambia un authorization code por tokens de la app de templates.
     * Se puede usar desde el callback o manualmente desde Postman.
     *
     * POST /api/ghl/exchange  { "code": "XXX" }
     */
    public function exchangeCodeEndpoint(\Illuminate\Http\Request $request)
    {
        $code = $request->input('code');

        if (empty($code)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El campo "code" es requerido.',
            ], 400);
        }

        $redirectUri = $request->input('redirect_uri', 'https://phpstack-1091339-3819555.cloudwaysapps.com/api/ghl/callback');

        return $this->exchangeCode($code, $redirectUri);
    }

    private function exchangeCode(string $code, string $redirectUri = '')
    {
        try {
            $configFile = base_path('config/gohighlevel.json');
            $config = json_decode(file_get_contents($configFile), true);

            $postData = http_build_query([
                'client_id' => $config['clientId'],
                'client_secret' => $config['clientSecret'],
                'grant_type' => 'authorization_code',
                'code' => $code,
                'user_type' => 'Location',
                'redirect_uri' => $redirectUri,
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $data = json_decode($response, true);

            if ($httpCode !== 200 || !isset($data['access_token'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GHL rechazó el code.',
                    'http_code' => $httpCode,
                    'ghl_error' => $data['error'] ?? null,
                    'ghl_message' => $data['error_description'] ?? $data['message'] ?? null,
                ], 500);
            }

            $config['access_token'] = $data['access_token'];
            $config['refreshToken'] = $data['refresh_token'];
            $config['expires_in'] = $data['expires_in'];
            $config['token_expires_at'] = time() + $data['expires_in'];
            if (isset($data['locationId'])) $config['locationId'] = $data['locationId'];
            if (isset($data['companyId'])) $config['companyId'] = $data['companyId'];
            if (isset($data['scope'])) $config['scope'] = $data['scope'];
            if (isset($data['userId'])) $config['userId'] = $data['userId'];

            @file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

            return response()->json([
                'status' => 'success',
                'message' => 'Tokens obtenidos y guardados en gohighlevel.json.',
                'bearer_token' => $data['access_token'],
                'token_info' => [
                    'expires_in' => $data['expires_in'],
                    'scope' => $data['scope'] ?? null,
                    'locationId' => $data['locationId'] ?? null,
                    'generated_at' => date('Y-m-d H:i:s'),
                ],
                'usage' => 'Authorization: Bearer ' . $data['access_token'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Renueva el Bearer token de la app de templates usando el refresh_token de gohighlevel.json.
     * No requiere redirect_uri ni flujo OAuth interactivo.
     *
     * GET /api/ghl/refresh-template-token
     */
    public function refreshTemplateToken()
    {
        try {
            $configFile = base_path('config/gohighlevel.json');

            if (!file_exists($configFile)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No existe config/gohighlevel.json',
                ], 500);
            }

            $config = json_decode(file_get_contents($configFile), true);

            if (empty($config['refreshToken'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No hay refreshToken en gohighlevel.json. Se necesita el flujo OAuth completo (authorize).',
                ], 500);
            }

            $postData = http_build_query([
                'client_id' => $config['clientId'],
                'client_secret' => $config['clientSecret'],
                'grant_type' => 'refresh_token',
                'refresh_token' => $config['refreshToken'],
                'user_type' => 'Location',
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $data = json_decode($response, true);

            if ($httpCode !== 200 || !isset($data['access_token'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GHL rechazó el refresh_token de templates.',
                    'http_code' => $httpCode,
                    'ghl_error' => $data['error'] ?? null,
                    'ghl_message' => $data['error_description'] ?? $data['message'] ?? null,
                    'refresh_token_tail' => '...' . substr($config['refreshToken'], -30),
                    'hint' => 'Si el refresh_token está vencido, se necesita re-autorizar con GET /api/ghl/authorize',
                ], 500);
            }

            $config['access_token'] = $data['access_token'];
            $config['refreshToken'] = $data['refresh_token'];
            $config['expires_in'] = $data['expires_in'];
            $config['token_expires_at'] = time() + $data['expires_in'];
            if (isset($data['locationId'])) $config['locationId'] = $data['locationId'];
            if (isset($data['companyId'])) $config['companyId'] = $data['companyId'];
            if (isset($data['scope'])) $config['scope'] = $data['scope'];
            if (isset($data['userId'])) $config['userId'] = $data['userId'];

            @file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

            return response()->json([
                'status' => 'success',
                'message' => 'Token de templates renovado y guardado en gohighlevel.json.',
                'bearer_token' => $data['access_token'],
                'token_info' => [
                    'expires_in' => $data['expires_in'],
                    'scope' => $data['scope'] ?? null,
                    'locationId' => $data['locationId'] ?? null,
                    'generated_at' => date('Y-m-d H:i:s'),
                ],
                'usage' => 'Authorization: Bearer ' . $data['access_token'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * TEMPORAL: Prueba directa con el token hardcodeado de GoHighLevelHelper (línea 43)
     * vs el token de la DB, para identificar cuál funciona.
     *
     * GET /api/ghl/debug
     */
    public function debug()
    {
        $config = ConfiguracionesGenerales::whereIn('nombre', [
            'ghl_client_id', 'ghl_client_secret', 'ghl_grant_type', 'ghl_refresh_token', 'ghl_user_type',
        ])->pluck('valor', 'nombre');

        $hardcodedToken = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoQ2xhc3MiOiJMb2NhdGlvbiIsImF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJzb3VyY2UiOiJJTlRFR1JBVElPTiIsInNvdXJjZUlkIjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5LW0xcXo1bHFxIiwiY2hhbm5lbCI6Ik9BVVRIIiwicHJpbWFyeUF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJvYXV0aE1ldGEiOnsic2NvcGVzIjpbImNvbnRhY3RzLnJlYWRvbmx5IiwiY29udGFjdHMud3JpdGUiXSwiY2xpZW50IjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5IiwidmVyc2lvbklkIjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5IiwiY2xpZW50S2V5IjoiNjZiM2I1MDk4OGY2OWFlNGU2ZGNhNWU5LW0xcXo1bHFxIn0sImlhdCI6MTc1NDUwOTAzOS44NDUsImV4cCI6MTc4NjA0NTAzOS44NDUsInVuaXF1ZUlkIjoiZTY3ODdjZGItMmUzYi00OTI5LWExNTYtMjliZmM2MjkwOTE3IiwidiI6IjIifQ.H2U3nje7Hs2rjbpsw19sk6XBeXdAQF7iaLqFbgSrdxO5_nneWu4tmrOp5vFe61jApaiKDs7X39yq60-CaClXClFBVgQWmIWKn1gpOCxEARWU3ryFJVqrxOstHY9w2B4sWxjKulNM4cq4I6wPXk3Ajan-yb0QvI5DtlWd7sbifaI693Hk-m-NsJ9kenSc6ijVi6zMegTfyMS5xfHWXyRv0Xz-1Wcn7G1lRwFLF8fxJbITDOLRvmO2m_Ly_lrsBz58Iuz3FVLtoDQPBzJ4grXQ57JXGmWtOSRePKQ8Wnt2wsn9rtXCLh8o9J0VMjIhr13XwjG8TbO5G7lR06mQkUmugQ5LCRoF7DH1UV659mzuuyIZaMh_H1k1rxma3Rn2rk7OaJTh94XrvceXwdwIxdIbGef7Y-OoO6L5GosK3q46dxVMMTGKspoNHj-5N0RQCxocBKFgPk6IBhyr07G0Pa8rq2DcOKXAvXVbAeZsfDHLtg8uwBcLoD6ON1e_mrL4CnBWuckJ3OQAoorhpagi6aoYpOWd-79SaYByGol3niyjmi4J80hT91BbADhSvRivcgL1cS5n1b7r1cPZe2e246IH-AupPPtPQRWQCRWOPsJn2QYOsuXI_yA_y5Wzs4d6yzjIPiIy5aenIh5PCTfe33FeHRgrPAEjrAYY82JRFd-XyWw';

        $results = [];

        foreach (['hardcoded' => $hardcodedToken, 'db' => $config['ghl_refresh_token'] ?? ''] as $source => $token) {
            $postData = http_build_query([
                'client_id' => $config['ghl_client_id'] ?? '',
                'client_secret' => $config['ghl_client_secret'] ?? '',
                'grant_type' => $config['ghl_grant_type'] ?? '',
                'code' => '',
                'refresh_token' => $token,
                'user_type' => $config['ghl_user_type'] ?? '',
                'redirect_uri' => '',
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $data = json_decode($response, true) ?? [];
            $results[$source] = [
                'http_code' => $httpCode,
                'success' => isset($data['access_token']),
                'error' => $data['error'] ?? null,
                'error_description' => $data['error_description'] ?? $data['message'] ?? null,
                'token_tail' => '...' . substr($token, -30),
            ];
        }

        return response()->json([
            'status' => 'debug',
            'message' => 'Comparación hardcoded vs DB refresh token',
            'results' => $results,
        ], 200);
    }

    /**
     * Llamada diagnóstica directa a GHL para ver el error real
     * (GoHighLevelHelper traga los errores y devuelve null).
     */
    private function diagnose()
    {
        $config = ConfiguracionesGenerales::whereIn('nombre', [
            'ghl_client_id', 'ghl_client_secret', 'ghl_grant_type',
            'ghl_refresh_token', 'ghl_user_type',
        ])->pluck('valor', 'nombre');

        $postData = http_build_query([
            'client_id' => $config['ghl_client_id'] ?? '',
            'client_secret' => $config['ghl_client_secret'] ?? '',
            'grant_type' => $config['ghl_grant_type'] ?? '',
            'code' => '',
            'refresh_token' => $config['ghl_refresh_token'] ?? '',
            'user_type' => $config['ghl_user_type'] ?? '',
            'redirect_uri' => '',
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $data = json_decode($response, true) ?? [];

        return response()->json([
            'status' => 'error',
            'message' => 'GoHighLevelHelper::getToken() devolvió null. Diagnóstico con credenciales de DB:',
            'diagnostic' => [
                'http_code' => $httpCode,
                'ghl_error' => $data['error'] ?? null,
                'ghl_message' => $data['error_description'] ?? $data['message'] ?? null,
                'db_refresh_token_preview' => substr($config['ghl_refresh_token'] ?? '', 0, 50) . '...',
                'db_client_id' => $config['ghl_client_id'] ?? '(vacío)',
                'db_grant_type' => $config['ghl_grant_type'] ?? '(vacío)',
            ],
            'nota' => 'GoHighLevelHelper usa un refresh_token HARDCODEADO en el código (línea 43), no el de la DB. Si la tienda funciona, ese hardcodeado es válido pero puede haber fallado al guardar en DB (línea 65-66).',
        ], 500);
    }
}
