<?php

namespace App\Services\Email;

use App\Services\TokenManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio de email usando GoHighLevel
 * Maneja webhooks y renovación automática de tokens
 */
class GoHighLevelEmailService implements EmailServiceInterface
{
    protected $tokenManager;
    protected $locationId;
    protected $maxRetries = 3;
    protected $retryDelay = 2; // segundos

    public function __construct(TokenManager $tokenManager = null)
    {
        $this->tokenManager = $tokenManager ?? new TokenManager();
        $this->locationId = env('GHL_LOCATION_ID', '40UecLU7dZ4KdLepJ7UR');
    }

    /**
     * Envía un email a través de GoHighLevel
     */
    public function send(EmailMessage $message): EmailResult
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                $attempt++;

                // Si el mensaje tiene webhook URL, usar webhook
                if ($message->webhookUrl) {
                    return $this->sendViaWebhook($message);
                }

                // Si no, intentar usar la API de GoHighLevel
                return $this->sendViaAPI($message);

            } catch (EmailException $e) {
                $lastException = $e;

                // Si no es reintentable, salir inmediatamente
                if (!$e->isRetryable()) {
                    Log::error('GoHighLevelEmailService: Error no reintentable', [
                        'error' => $e->getMessage(),
                        'service' => $e->getService(),
                        'metadata' => $e->getMetadata()
                    ]);
                    break;
                }

                // Si es el último intento, no esperar
                if ($attempt >= $this->maxRetries) {
                    break;
                }

                // Esperar antes de reintentar
                Log::warning("GoHighLevelEmailService: Reintentando envío (intento {$attempt}/{$this->maxRetries})", [
                    'error' => $e->getMessage()
                ]);
                sleep($this->retryDelay * $attempt); // Backoff exponencial

            } catch (Exception $e) {
                $lastException = new EmailException(
                    'Error inesperado: ' . $e->getMessage(),
                    0,
                    $e,
                    true,
                    $this->getName()
                );
                break;
            }
        }

        // Si llegamos aquí, todos los intentos fallaron
        return EmailResult::error(
            'Error al enviar email después de ' . $this->maxRetries . ' intentos: ' . ($lastException ? $lastException->getMessage() : 'Error desconocido'),
            $lastException,
            false,
            $this->getName()
        );
    }

    /**
     * Envía email vía webhook (método más común)
     */
    protected function sendViaWebhook(EmailMessage $message): EmailResult
    {
        try {
            // Preparar payload
            $payload = $message->webhookPayload;

            // Agregar información básica si no está presente
            if (!isset($payload['email'])) {
                $payload['email'] = is_array($message->to) ? $message->to[0] : $message->to;
            }

            // Si hay adjuntos, convertirlos a URLs (GoHighLevel no soporta adjuntos directos)
            if (!empty($message->attachments)) {
                $attachmentManager = new \App\Services\Email\AttachmentManager();
                $attachmentUrls = $attachmentManager->prepareForGoHighLevel($message->attachments);
                
                if (!empty($attachmentUrls)) {
                    $payload['attachmentUrls'] = $attachmentUrls;
                    Log::info('GoHighLevelEmailService: Adjuntos convertidos a URLs', [
                        'count' => count($attachmentUrls),
                        'urls' => $attachmentUrls
                    ]);
                }
            }

            // Agregar CC/BCC si existen
            if (!empty($message->cc)) {
                $payload['emailCC'] = $message->cc;
            }

            if (!empty($message->bcc)) {
                $payload['emailBCC'] = $message->bcc;
            }

            // Agregar metadata
            if (!empty($message->metadata)) {
                $payload['metadata'] = $message->metadata;
            }

            Log::info('GoHighLevelEmailService: Enviando webhook', [
                'url' => $message->webhookUrl,
                'to' => $message->to,
                'subject' => $message->subject
            ]);

            // Enviar webhook
            $response = Http::timeout(30)
                ->retry(2, 1000) // 2 reintentos con 1 segundo de delay
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($message->webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('GoHighLevelEmailService: Webhook enviado exitosamente', [
                    'url' => $message->webhookUrl,
                    'response' => $response->json()
                ]);

                return EmailResult::success(
                    'Email enviado exitosamente vía webhook',
                    $response->json(),
                    $this->getName()
                );
            }

            // Si el error es 401, puede ser token expirado
            if ($response->status() === 401) {
                throw new EmailException(
                    'Token de GoHighLevel expirado o inválido',
                    401,
                    null,
                    true, // Reintentable
                    $this->getName(),
                    ['http_code' => 401, 'response' => $response->body()]
                );
            }

            // Si el error es 429 (rate limit), es reintentable
            if ($response->status() === 429) {
                throw new EmailException(
                    'Rate limit alcanzado en GoHighLevel',
                    429,
                    null,
                    true,
                    $this->getName(),
                    ['http_code' => 429]
                );
            }

            // Otros errores HTTP
            throw new EmailException(
                'Error HTTP al enviar webhook: ' . $response->status(),
                $response->status(),
                null,
                $response->status() >= 500, // Solo errores 5xx son reintentables
                $this->getName(),
                ['http_code' => $response->status(), 'response' => $response->body()]
            );

        } catch (EmailException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new EmailException(
                'Error al enviar webhook: ' . $e->getMessage(),
                0,
                $e,
                true,
                $this->getName()
            );
        }
    }

    /**
     * Envía email vía API de GoHighLevel (requiere token válido)
     */
    protected function sendViaAPI(EmailMessage $message): EmailResult
    {
        try {
            // Obtener token válido (se renueva automáticamente si es necesario)
            $accessToken = $this->tokenManager->getValidToken();

            // Preparar payload para la API
            $payload = [
                'locationId' => $this->locationId,
                'to' => is_array($message->to) ? $message->to : [$message->to],
                'subject' => $message->subject,
                'htmlBody' => $message->htmlBody ?? $message->body,
            ];

            if (!empty($message->cc)) {
                $payload['cc'] = $message->cc;
            }

            if (!empty($message->bcc)) {
                $payload['bcc'] = $message->bcc;
            }

            Log::info('GoHighLevelEmailService: Enviando vía API', [
                'to' => $message->to,
                'subject' => $message->subject
            ]);

            // Enviar a la API
            $response = Http::timeout(30)
                ->retry(2, 1000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Version' => '2021-07-28',
                ])
                ->post('https://services.leadconnectorhq.com/emails', $payload);

            if ($response->successful()) {
                return EmailResult::success(
                    'Email enviado exitosamente vía API',
                    $response->json(),
                    $this->getName()
                );
            }

            // Manejar errores de token
            if ($response->status() === 401) {
                // Intentar renovar token y reintentar una vez más
                try {
                    $accessToken = $this->tokenManager->refreshToken();
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                        'Version' => '2021-07-28',
                    ])->post('https://services.leadconnectorhq.com/emails', $payload);

                    if ($response->successful()) {
                        return EmailResult::success(
                            'Email enviado exitosamente después de renovar token',
                            $response->json(),
                            $this->getName()
                        );
                    }
                } catch (Exception $e) {
                    throw new EmailException(
                        'Error al renovar token: ' . $e->getMessage(),
                        401,
                        $e,
                        false, // No reintentable si falla la renovación
                        $this->getName()
                    );
                }
            }

            throw new EmailException(
                'Error HTTP al enviar email: ' . $response->status(),
                $response->status(),
                null,
                $response->status() >= 500,
                $this->getName(),
                ['http_code' => $response->status(), 'response' => $response->body()]
            );

        } catch (EmailException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new EmailException(
                'Error al enviar email vía API: ' . $e->getMessage(),
                0,
                $e,
                true,
                $this->getName()
            );
        }
    }

    /**
     * Verifica si el servicio está disponible
     */
    public function isAvailable(): bool
    {
        try {
            // Verificar que el token manager funcione
            $this->tokenManager->getValidToken();
            return true;
        } catch (Exception $e) {
            Log::error('GoHighLevelEmailService: Servicio no disponible', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtiene el nombre del servicio
     */
    public function getName(): string
    {
        return 'GoHighLevel';
    }
}
