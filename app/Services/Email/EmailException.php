<?php

namespace App\Services\Email;

use Exception;

/**
 * Excepción personalizada para errores de email
 */
class EmailException extends Exception
{
    protected $retryable;
    protected $service;
    protected $metadata = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        Exception $previous = null,
        bool $retryable = false,
        string $service = '',
        array $metadata = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->retryable = $retryable;
        $this->service = $service;
        $this->metadata = $metadata;
    }

    /**
     * Verifica si el error es reintentable
     */
    public function isRetryable(): bool
    {
        return $this->retryable;
    }

    /**
     * Obtiene el servicio que causó el error
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Obtiene metadata adicional
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
