<?php

namespace App\Services;

use App\Models\ConfiguracionesGenerales;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Token Manager para GoHighLevel
 * Maneja la renovación automática de tokens de acceso.
 * Fuente primaria: config/gohighlevel.json
 * Fallback: tabla configuracionesgenerales (campo ghl_refresh_token)
 */
class TokenManager
{
    private $configFile;
    private $config;

    public function __construct($configFile = null) {
        // Si no se especifica archivo, usar el de la carpeta config de Laravel
                    //echo base_path('app/config/gohighlevel.json');die;

        if ($configFile === null) {
            $this->configFile = base_path('config/gohighlevel.json');
        } else {
            $this->configFile = $configFile;
        }
        $this->loadConfig();
    }

    /**
     * Carga la configuración desde el archivo JSON
     */
    private function loadConfig() {
        if (!file_exists($this->configFile)) {
            throw new Exception('Archivo de configuración no encontrado: ' . $this->configFile);
        }

        $this->config = json_decode(file_get_contents($this->configFile), true);
        if ($this->config === null) {
            throw new Exception('Error al decodificar el archivo de configuración JSON');
        }
    }

    /**
     * Guarda la configuración en el archivo JSON.
     * Si no tiene permisos de escritura, loguea warning pero no interrumpe el flujo.
     */
    private function saveConfig() {
        $result = @file_put_contents($this->configFile, json_encode($this->config, JSON_PRETTY_PRINT));
        if ($result === false) {
            Log::warning('TokenManager: no se pudo escribir ' . $this->configFile . ' (permisos). El token se usará en memoria.');
        }
    }

    /**
     * Verifica si el token actual es válido
     */
    public function isTokenValid() {
        if (empty($this->config['access_token'])) {
            return false;
        }

        // Verifica si el token ha expirado (con un margen de 5 minutos)
        if (isset($this->config['token_expires_at'])) {
            $currentTime = time();
            $expiresAt = $this->config['token_expires_at'];
            $margin = 300; // 5 minutos en segundos

            return ($currentTime < ($expiresAt - $margin));
        }

        return false;
    }

    /**
     * Obtiene un token válido, renovándolo si es necesario.
     * Si el refresh token del JSON falla, intenta con el de la DB como fallback.
     */
    public function getValidToken() {
        if ($this->isTokenValid()) {
            return $this->config['access_token'];
        }

        if (empty($this->config['refreshToken'])) {
            throw new Exception('No hay refresh token disponible. Necesitas autorización inicial.');
        }

        try {
            return $this->refreshToken($this->config['refreshToken']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $shouldFallback = strpos($msg, 'invalid_grant') !== false
                           || strpos($msg, 'Invalid client credentials') !== false
                           || strpos($msg, 'UnAuthorized') !== false;

            if ($shouldFallback) {
                Log::warning('TokenManager: JSON credentials fallaron (' . $msg . '), intentando con DB');
                return $this->tryRefreshFromDatabase();
            }
            throw $e;
        }
    }

    /**
     * Permite setear un refresh token manualmente y lo persiste en el JSON.
     */
    public function setRefreshToken(string $refreshToken) {
        $this->config['refreshToken'] = $refreshToken;
        $this->config['access_token'] = '';
        $this->config['token_expires_at'] = 0;
        $this->saveConfig();
    }

    /**
     * Intenta renovar usando las credenciales completas de la DB (tabla configuracionesgenerales).
     * La DB puede tener una app de GHL distinta a la del JSON.
     */
    private function tryRefreshFromDatabase() {
        try {
            $campos = [
                'ghl_client_id',
                'ghl_client_secret',
                'ghl_refresh_token',
                'ghl_user_type',
            ];
            $dbConfig = ConfiguracionesGenerales::whereIn('nombre', $campos)
                ->pluck('valor', 'nombre');
        } catch (Exception $e) {
            throw new Exception('Refresh token del JSON inválido y no se pudo leer la DB: ' . $e->getMessage());
        }

        $dbRefreshToken = $dbConfig['ghl_refresh_token'] ?? '';

        if (empty($dbRefreshToken)) {
            throw new Exception(
                'Refresh token inválido en JSON y vacío en DB. '
                . 'Necesitas re-autorizar la app en GoHighLevel o setear uno nuevo via POST /api/ghl/token.'
            );
        }

        $dbClientId = $dbConfig['ghl_client_id'] ?? '';
        $dbClientSecret = $dbConfig['ghl_client_secret'] ?? '';
        $dbUserType = $dbConfig['ghl_user_type'] ?? 'Location';

        if (empty($dbClientId) || empty($dbClientSecret)) {
            throw new Exception(
                'Credenciales de GHL incompletas en la DB (ghl_client_id / ghl_client_secret vacíos).'
            );
        }

        Log::info('TokenManager: usando credenciales completas de la DB como fallback');

        return $this->refreshToken($dbRefreshToken, $dbClientId, $dbClientSecret, $dbUserType);
    }

    /**
     * Renueva el token contra la API de GoHighLevel.
     * Sincroniza el nuevo refresh token tanto en el JSON como en la DB.
     *
     * @param string $refreshToken
     * @param string|null $clientId    Si es null, usa el del JSON
     * @param string|null $clientSecret Si es null, usa el del JSON
     * @param string $userType
     */
    private function refreshToken(string $refreshToken, ?string $clientId = null, ?string $clientSecret = null, string $userType = 'Location') {
        $data = array(
            'client_id' => $clientId ?? $this->config['clientId'],
            'client_secret' => $clientSecret ?? $this->config['clientSecret'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'user_type' => $userType
        );

        $postData = http_build_query($data);

        $curl = curl_init();

        $options = array(
            CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ),
        );

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            throw new Exception('Error cURL: ' . curl_error($curl));
        }

        curl_close($curl);

        $responseData = json_decode($response, true);

        if ($responseData === null) {
            throw new Exception('Error al decodificar respuesta JSON');
        }

        if ($httpCode !== 200) {
            $error = isset($responseData['error']) ? $responseData['error'] : 'Error desconocido';
            $description = isset($responseData['error_description']) ? $responseData['error_description'] : '';
            throw new Exception("Error al renovar token: $error - $description");
        }

        $this->config['access_token'] = $responseData['access_token'];
        $this->config['refreshToken'] = $responseData['refresh_token'];
        $this->config['expires_in'] = $responseData['expires_in'];
        $this->config['token_expires_at'] = time() + $responseData['expires_in'];
        $this->config['refresh_token_expires_at'] = $this->extractJwtExpiry($responseData['refresh_token']);

        $this->saveConfig();
        $this->syncRefreshTokenToDatabase($responseData['refresh_token']);
        $this->warnIfRefreshTokenExpiring();

        Log::info('TokenManager: token renovado exitosamente');

        return $responseData['access_token'];
    }

    /**
     * Extrae el campo "exp" del payload de un JWT sin verificar firma.
     * Retorna el timestamp de expiración, o 0 si no se puede decodificar.
     */
    private function extractJwtExpiry(string $jwt): int {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return 0;
        }
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        return isset($payload['exp']) ? (int) $payload['exp'] : 0;
    }

    /**
     * Loguea warning si el refresh token vence en menos de 30 días.
     * Loguea critical si vence en menos de 7 días.
     */
    private function warnIfRefreshTokenExpiring() {
        $expiresAt = $this->config['refresh_token_expires_at'] ?? 0;
        if ($expiresAt <= 0) {
            return;
        }

        $daysLeft = ($expiresAt - time()) / 86400;

        if ($daysLeft < 7) {
            Log::critical("TokenManager: REFRESH TOKEN vence en " . round($daysLeft, 1) . " días. Re-autorizar urgente con GET /api/ghl/authorize");
        } elseif ($daysLeft < 30) {
            Log::warning("TokenManager: Refresh token vence en " . round($daysLeft, 1) . " días. Planificar re-autorización.");
        }
    }

    /**
     * Mantiene sincronizado el refresh token en la DB para evitar desincronización.
     */
    private function syncRefreshTokenToDatabase(string $newRefreshToken) {
        try {
            $registro = ConfiguracionesGenerales::where('nombre', 'ghl_refresh_token')->first();
            if ($registro) {
                $registro->valor = $newRefreshToken;
                $registro->save();
            }
        } catch (Exception $e) {
            Log::warning('TokenManager: no se pudo sincronizar refresh token a la DB: ' . $e->getMessage());
        }
    }

    /**
     * Intercambia un authorization code por tokens (flujo OAuth inicial).
     */
    public function exchangeAuthorizationCode(string $code) {
        $data = array(
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'grant_type' => 'authorization_code',
            'code' => $code,
            'user_type' => 'Location'
        );

        $postData = http_build_query($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            throw new Exception('Error cURL: ' . curl_error($curl));
        }
        curl_close($curl);

        $responseData = json_decode($response, true);
        if ($responseData === null) {
            throw new Exception('Error al decodificar respuesta JSON');
        }

        if ($httpCode !== 200) {
            $error = $responseData['error'] ?? 'Error desconocido';
            $description = $responseData['error_description'] ?? $responseData['message'] ?? '';
            throw new Exception("Error al intercambiar code: $error - $description");
        }

        $this->config['access_token'] = $responseData['access_token'];
        $this->config['refreshToken'] = $responseData['refresh_token'];
        $this->config['expires_in'] = $responseData['expires_in'];
        $this->config['token_expires_at'] = time() + $responseData['expires_in'];

        if (isset($responseData['locationId'])) {
            $this->config['locationId'] = $responseData['locationId'];
        }
        if (isset($responseData['companyId'])) {
            $this->config['companyId'] = $responseData['companyId'];
        }
        if (isset($responseData['scope'])) {
            $this->config['scope'] = $responseData['scope'];
        }
        if (isset($responseData['userId'])) {
            $this->config['userId'] = $responseData['userId'];
        }

        $this->saveConfig();
        $this->syncRefreshTokenToDatabase($responseData['refresh_token']);

        Log::info('TokenManager: authorization code intercambiado exitosamente');

        return $responseData['access_token'];
    }

    /**
     * Devuelve la URL de autorización OAuth para re-autorizar la app en GHL.
     */
    public function getAuthorizationUrl(string $redirectUri) {
        $params = http_build_query([
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'client_id' => $this->config['clientId'],
            'scope' => $this->config['scope'] ?? '',
        ]);

        return ($this->config['baseUrl'] ?? 'https://marketplace.gohighlevel.com')
            . '/oauth/chooselocation?' . $params;
    }

    /**
     * Devuelve el clientId y scopes configurados.
     */
    public function getAppInfo() {
        return [
            'clientId' => $this->config['clientId'] ?? '',
            'baseUrl' => $this->config['baseUrl'] ?? '',
            'scope' => $this->config['scope'] ?? '',
            'locationId' => $this->config['locationId'] ?? '',
        ];
    }

    /**
     * Obtiene información del token actual
     */
    public function getTokenInfo() {
        return array(
            'access_token' => $this->config['access_token'] ?? '',
            'refresh_token' => $this->config['refreshToken'] ?? '',
            'expires_in' => $this->config['expires_in'] ?? 0,
            'token_expires_at' => $this->config['token_expires_at'] ?? 0,
            'is_valid' => $this->isTokenValid(),
            'current_time' => time()
        );
    }
}
