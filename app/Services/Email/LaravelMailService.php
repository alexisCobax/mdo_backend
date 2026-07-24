<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio de email usando Laravel Mail (SMTP tradicional)
 */
class LaravelMailService implements EmailServiceInterface
{
    protected $maxRetries = 2;
    protected $retryDelay = 1;

    public function send(EmailMessage $message): EmailResult
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                $attempt++;

                // Si hay template, usar Mailable personalizado
                if ($message->template) {
                    return $this->sendWithTemplate($message);
                }

                // Si hay adjuntos, crear Mailable con adjuntos
                if (!empty($message->attachments)) {
                    return $this->sendWithAttachments($message);
                }

                // Envío simple
                return $this->sendSimple($message);

            } catch (EmailException $e) {
                $lastException = $e;

                if (!$e->isRetryable() || $attempt >= $this->maxRetries) {
                    break;
                }

                Log::warning("LaravelMailService: Reintentando envío (intento {$attempt}/{$this->maxRetries})", [
                    'error' => $e->getMessage()
                ]);
                sleep($this->retryDelay * $attempt);

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

        return EmailResult::error(
            'Error al enviar email después de ' . $this->maxRetries . ' intentos: ' . ($lastException ? $lastException->getMessage() : 'Error desconocido'),
            $lastException,
            false,
            $this->getName()
        );
    }

    /**
     * Envío simple sin adjuntos
     */
    protected function sendSimple(EmailMessage $message): EmailResult
    {
        try {
            $mailable = new \App\Mail\SimpleEmail(
                $message->subject,
                $message->htmlBody ?? $message->body,
                $message->body
            );

            // Configurar destinatarios
            $mail = Mail::to($message->to);

            if (!empty($message->cc)) {
                $mail->cc($message->cc);
            }

            if (!empty($message->bcc)) {
                $mail->bcc($message->bcc);
            }

            $mail->send($mailable);

            Log::info('LaravelMailService: Email enviado exitosamente', [
                'to' => $message->to,
                'subject' => $message->subject
            ]);

            return EmailResult::success(
                'Email enviado exitosamente',
                null,
                $this->getName()
            );

        } catch (Exception $e) {
            throw new EmailException(
                'Error al enviar email: ' . $e->getMessage(),
                0,
                $e,
                true,
                $this->getName()
            );
        }
    }

    /**
     * Envío con adjuntos
     */
    protected function sendWithAttachments(EmailMessage $message): EmailResult
    {
        try {
            $mailable = new \App\Mail\EmailWithAttachments(
                $message->subject,
                $message->htmlBody ?? $message->body,
                $message->body,
                $message->attachments
            );

            $mail = Mail::to($message->to);

            if (!empty($message->cc)) {
                $mail->cc($message->cc);
            }

            if (!empty($message->bcc)) {
                $mail->bcc($message->bcc);
            }

            $mail->send($mailable);

            return EmailResult::success(
                'Email con adjuntos enviado exitosamente',
                null,
                $this->getName()
            );

        } catch (Exception $e) {
            throw new EmailException(
                'Error al enviar email con adjuntos: ' . $e->getMessage(),
                0,
                $e,
                true,
                $this->getName()
            );
        }
    }

    /**
     * Envío con template
     */
    protected function sendWithTemplate(EmailMessage $message): EmailResult
    {
        try {
            // Buscar la clase Mailable correspondiente al template
            $mailableClass = $this->getMailableClass($message->template);

            if (!class_exists($mailableClass)) {
                throw new EmailException(
                    "Clase Mailable no encontrada: {$mailableClass}",
                    0,
                    null,
                    false,
                    $this->getName()
                );
            }

            $mailable = new $mailableClass(
                $message->templateData,
                $message->subject ?? ''
            );

            // Agregar adjuntos si existen (con validación)
            $attachmentManager = new \App\Services\Email\AttachmentManager();
            $validatedAttachments = $attachmentManager->prepareForLaravelMail($message->attachments);

            foreach ($validatedAttachments as $attachment) {
                try {
                    $mailable->attach($attachment['path'], [
                        'as' => $attachment['name']
                    ]);
                } catch (Exception $e) {
                    Log::warning('LaravelMailService: Error al adjuntar archivo', [
                        'path' => $attachment['path'],
                        'error' => $e->getMessage()
                    ]);
                    // Continuar con otros adjuntos
                }
            }

            $mail = Mail::to($message->to);

            if (!empty($message->cc)) {
                $mail->cc($message->cc);
            }

            if (!empty($message->bcc)) {
                $mail->bcc($message->bcc);
            }

            $mail->send($mailable);

            return EmailResult::success(
                'Email con template enviado exitosamente',
                null,
                $this->getName()
            );

        } catch (EmailException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new EmailException(
                'Error al enviar email con template: ' . $e->getMessage(),
                0,
                $e,
                true,
                $this->getName()
            );
        }
    }

    /**
     * Obtiene la clase Mailable correspondiente al template
     */
    protected function getMailableClass(string $template): string
    {
        // Mapeo de templates a clases Mailable
        $mapping = [
            'mdo.emailCotizacion' => \App\Mail\EnvioCotizacionMailConAdjunto::class,
            'mdo.emailInvoice' => \App\Mail\EnvioMailInvoice::class,
            'mdo.emailClienteAprobado' => \App\Mail\EnvioMailComunicado::class,
            'mdo.emailClienteProspecto' => \App\Mail\EnvioMailComunicado::class,
            'mdo.emailCambiarClave' => \App\Mail\EnvioMailCambiarClave::class,
        ];

        return $mapping[$template] ?? 'App\\Mail\\' . class_basename($template);
    }

    public function isAvailable(): bool
    {
        try {
            // Verificar configuración de mail
            $mailer = config('mail.default');
            return !empty($mailer);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'LaravelMail';
    }
}
