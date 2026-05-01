<?php

namespace App\Http\Controllers;

use App\Services\TokenManager;

class TestTokenController extends Controller
{
    public function testToken()
    {
        try {
            $tokenManager = new TokenManager();

            // Obtener información del token
            $tokenInfo = $tokenManager->getTokenInfo();

            // Obtener un token válido
            $validToken = $tokenManager->getValidToken();

            return response()->json([
                'status' => 'success',
                'message' => 'Token Manager funcionando correctamente',
                'token_info' => $tokenInfo,
                'valid_token' => substr($validToken, 0, 50) . '...',
                'token_length' => strlen($validToken)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testTemplateUpdate()
    {
        try {
            $tokenManager = new TokenManager();
            $accessToken = $tokenManager->getValidToken();

            // HTML de prueba simple
            $html = '<!DOCTYPE html><html><body><h1>Test Template</h1><p>Este es un test del template.</p></body></html>';

            $payload = [
                "locationId"   => "40UecLU7dZ4KdLepJ7UR",
                "templateId"   => "689e3e8af892621e5c9bbd69",
                "updatedBy"    => "zYy3YOUuHxgomU1uYJty",
                "dnd"          => "{elements:[], attrs:{}, templateSettings:{}}",
                "html"         => $html,
                "editorType"   => "html",
                "previewText"  => "Test template",
                "isPlainText"  => false
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://services.leadconnectorhq.com/emails/builder/data',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Version: 2021-07-28',
                    'Authorization: Bearer ' . $accessToken
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $isSuccess = ($httpCode === 200 || $httpCode === 201);

            return response()->json([
                'status' => $isSuccess ? 'success' : 'error',
                'message' => $isSuccess ? 'Test de actualización de template completado exitosamente' : 'Error en la actualización del template',
                'http_code' => $httpCode,
                'response' => json_decode($response, true),
                'token_used' => substr($accessToken, 0, 50) . '...',
                'note' => $httpCode === 201 ? 'HTTP 201 = Created (éxito)' : ''
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
