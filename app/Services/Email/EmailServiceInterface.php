<?php

namespace App\Services\Email;

/**
 * Interface para servicios de envío de email
 * Permite diferentes implementaciones (GoHighLevel, Laravel Mail, etc.)
 */
interface EmailServiceInterface
{
    /**
     * Envía un email
     *
     * @param EmailMessage $message
     * @return EmailResult
     * @throws EmailException
     */
    public function send(EmailMessage $message): EmailResult;

    /**
     * Verifica si el servicio está disponible
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Obtiene el nombre del servicio
     *
     * @return string
     */
    public function getName(): string;
}
