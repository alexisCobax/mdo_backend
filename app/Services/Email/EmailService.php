<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio unificado de email
 * Gestiona múltiples proveedores y fallbacks automáticos
 */
class EmailService
{
    protected $services = [];
    protected $defaultService = null;
    protected $fallbackServices = [];

    public function __construct()
    {
        // Registrar servicios disponibles
        $this->registerServices();
    }

    /**
     * Registra los servicios de email disponibles
     */
    protected function registerServices()
    {
        // GoHighLevel como servicio principal (si está disponible)
        try {
            $ghlService = new GoHighLevelEmailService();
            if ($ghlService->isAvailable()) {
                $this->services['gohighlevel'] = $ghlService;
                $this->defaultService = 'gohighlevel';
            }
        } catch (Exception $e) {
            Log::warning('EmailService: GoHighLevel no disponible', [
                'error' => $e->getMessage()
            ]);
        }

        // Laravel Mail como fallback
        try {
            $laravelService = new LaravelMailService();
            if ($laravelService->isAvailable()) {
                $this->services['laravel'] = $laravelService;
                if (!$this->defaultService) {
                    $this->defaultService = 'laravel';
                }
                $this->fallbackServices[] = 'laravel';
            }
        } catch (Exception $e) {
            Log::warning('EmailService: Laravel Mail no disponible', [
                'error' => $e->getMessage()
            ]);
        }

        // Si no hay servicios disponibles, lanzar excepción
        if (empty($this->services)) {
            throw new EmailException(
                'No hay servicios de email disponibles',
                0,
                null,
                false,
                'EmailService'
            );
        }
    }

    /**
     * Envía un email usando el servicio apropiado
     *
     * @param EmailMessage|array $message
     * @param string|null $preferredService Servicio preferido ('gohighlevel', 'laravel')
     * @return EmailResult
     */
    public function send($message, ?string $preferredService = null): EmailResult
    {
        // Convertir array a EmailMessage si es necesario
        if (is_array($message)) {
            $message = EmailMessage::fromArray($message);
        }

        if (!($message instanceof EmailMessage)) {
            throw new EmailException(
                'El mensaje debe ser una instancia de EmailMessage o un array',
                0,
                null,
                false,
                'EmailService'
            );
        }

        // Determinar qué servicio usar
        $serviceName = $this->determineService($message, $preferredService);
        $servicesToTry = $this->getServicesToTry($serviceName);

        $lastResult = null;
        $lastException = null;

        // Intentar con cada servicio hasta que uno funcione
        foreach ($servicesToTry as $serviceName) {
            if (!isset($this->services[$serviceName])) {
                continue;
            }

            $service = $this->services[$serviceName];

            try {
                Log::info("EmailService: Intentando enviar con {$serviceName}", [
                    'to' => $message->to,
                    'subject' => $message->subject
                ]);

                $result = $service->send($message);

                if ($result->isSuccess()) {
                    Log::info("EmailService: Email enviado exitosamente con {$serviceName}", [
                        'to' => $message->to,
                        'subject' => $message->subject
                    ]);
                    return $result;
                }

                $lastResult = $result;

                // Si no es reintentable, no probar otros servicios
                if (!$result->isRetryable()) {
                    break;
                }

            } catch (EmailException $e) {
                $lastException = $e;
                $lastResult = EmailResult::error(
                    $e->getMessage(),
                    $e,
                    $e->isRetryable(),
                    $serviceName
                );

                // Si no es reintentable, no probar otros servicios
                if (!$e->isRetryable()) {
                    break;
                }

            } catch (Exception $e) {
                $lastException = $e;
                Log::error("EmailService: Error inesperado con {$serviceName}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Si llegamos aquí, todos los servicios fallaron
        $errorMessage = 'Error al enviar email. Todos los servicios fallaron.';
        if ($lastException) {
            $errorMessage .= ' Último error: ' . $lastException->getMessage();
        }

        Log::error('EmailService: Fallo en todos los servicios', [
            'to' => $message->to,
            'subject' => $message->subject,
            'last_error' => $lastException ? $lastException->getMessage() : null
        ]);

        return EmailResult::error(
            $errorMessage,
            $lastException,
            false,
            'EmailService'
        );
    }

    /**
     * Determina qué servicio usar basado en el mensaje
     */
    protected function determineService(EmailMessage $message, ?string $preferredService): string
    {
        // Si hay webhook URL, usar GoHighLevel
        if ($message->webhookUrl && isset($this->services['gohighlevel'])) {
            return 'gohighlevel';
        }

        // Si hay preferencia, usarla
        if ($preferredService && isset($this->services[$preferredService])) {
            return $preferredService;
        }

        // Usar servicio por defecto
        return $this->defaultService ?? array_key_first($this->services);
    }

    /**
     * Obtiene la lista de servicios a intentar en orden
     */
    protected function getServicesToTry(string $primaryService): array
    {
        $services = [$primaryService];

        // Agregar servicios de fallback
        foreach ($this->fallbackServices as $fallback) {
            if ($fallback !== $primaryService && !in_array($fallback, $services)) {
                $services[] = $fallback;
            }
        }

        return $services;
    }

    /**
     * Envía un email de forma simple (helper method)
     */
    public function sendSimple(
        $to,
        string $subject,
        string $body,
        ?string $htmlBody = null,
        ?string $service = null
    ): EmailResult {
        $message = new EmailMessage($to, $subject, $body, $htmlBody);
        return $this->send($message, $service);
    }

    /**
     * Envía un email vía webhook de GoHighLevel (helper method)
     */
    public function sendWebhook(
        string $webhookUrl,
        array $payload,
        ?string $to = null,
        ?string $subject = null
    ): EmailResult {
        $message = new EmailMessage($to ?? []);
        $message->webhookUrl = $webhookUrl;
        $message->webhookPayload = $payload;
        $message->subject = $subject;

        return $this->send($message, 'gohighlevel');
    }

    /**
     * Envía un email con template (helper method)
     */
    public function sendTemplate(
        $to,
        string $template,
        array $templateData,
        ?string $subject = null,
        array $attachments = [],
        ?string $service = null
    ): EmailResult {
        $message = new EmailMessage($to, $subject);
        $message->template = $template;
        $message->templateData = $templateData;

        foreach ($attachments as $attachment) {
            if (is_string($attachment)) {
                $message->addAttachment($attachment);
            } elseif (is_array($attachment)) {
                $message->addAttachment($attachment['path'], $attachment['name'] ?? null);
            }
        }

        return $this->send($message, $service);
    }

    /**
     * Obtiene el estado de los servicios
     */
    public function getServicesStatus(): array
    {
        $status = [];

        foreach ($this->services as $name => $service) {
            $status[$name] = [
                'available' => $service->isAvailable(),
                'name' => $service->getName()
            ];
        }

        return $status;
    }
}
