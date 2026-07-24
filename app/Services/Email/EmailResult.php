<?php

namespace App\Services\Email;

/**
 * Resultado de un envío de email
 */
class EmailResult
{
    public $success;
    public $message;
    public $service;
    public $response;
    public $error;
    public $retryable;
    public $metadata = [];

    public function __construct(
        bool $success,
        string $message = '',
        string $service = '',
        $response = null,
        $error = null,
        bool $retryable = false
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->service = $service;
        $this->response = $response;
        $this->error = $error;
        $this->retryable = $retryable;
    }

    /**
     * Factory para resultado exitoso
     */
    public static function success(string $message = '', $response = null, string $service = ''): self
    {
        return new self(true, $message, $service, $response);
    }

    /**
     * Factory para resultado con error
     */
    public static function error(string $message, $error = null, bool $retryable = false, string $service = ''): self
    {
        return new self(false, $message, $service, null, $error, $retryable);
    }

    /**
     * Verifica si el resultado es exitoso
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Verifica si se puede reintentar
     */
    public function isRetryable(): bool
    {
        return $this->retryable;
    }
}
