<?php

namespace App\Services\Email;

/**
 * DTO (Data Transfer Object) para mensajes de email
 * Contiene toda la información necesaria para enviar un email
 */
class EmailMessage
{
    public $to;
    public $cc = [];
    public $bcc = [];
    public $subject;
    public $body;
    public $htmlBody;
    public $attachments = [];
    public $template = null;
    public $templateData = [];
    public $webhookUrl = null;
    public $webhookPayload = [];
    public $metadata = [];

    /**
     * Constructor
     */
    public function __construct(
        $to,
        $subject = null,
        $body = null,
        $htmlBody = null
    ) {
        $this->to = is_array($to) ? $to : [$to];
        $this->subject = $subject;
        $this->body = $body;
        $this->htmlBody = $htmlBody;
    }

    /**
     * Factory method para crear mensaje desde array
     */
    public static function fromArray(array $data): self
    {
        $message = new self(
            $data['to'] ?? [],
            $data['subject'] ?? null,
            $data['body'] ?? null,
            $data['html_body'] ?? $data['htmlBody'] ?? null
        );

        if (isset($data['cc'])) {
            $message->cc = is_array($data['cc']) ? $data['cc'] : [$data['cc']];
        }

        if (isset($data['bcc'])) {
            $message->bcc = is_array($data['bcc']) ? $data['bcc'] : [$data['bcc']];
        }

        if (isset($data['attachments'])) {
            $message->attachments = $data['attachments'];
        }

        if (isset($data['template'])) {
            $message->template = $data['template'];
        }

        if (isset($data['template_data'])) {
            $message->templateData = $data['template_data'];
        }

        if (isset($data['webhook_url'])) {
            $message->webhookUrl = $data['webhook_url'];
        }

        if (isset($data['webhook_payload'])) {
            $message->webhookPayload = $data['webhook_payload'];
        }

        if (isset($data['metadata'])) {
            $message->metadata = $data['metadata'];
        }

        return $message;
    }

    /**
     * Agrega un destinatario
     */
    public function addTo($email): self
    {
        if (!in_array($email, $this->to)) {
            $this->to[] = $email;
        }
        return $this;
    }

    /**
     * Agrega un CC
     */
    public function addCc($email): self
    {
        if (!in_array($email, $this->cc)) {
            $this->cc[] = $email;
        }
        return $this;
    }

    /**
     * Agrega un BCC
     */
    public function addBcc($email): self
    {
        if (!in_array($email, $this->bcc)) {
            $this->bcc[] = $email;
        }
        return $this;
    }

    /**
     * Agrega un adjunto
     */
    public function addAttachment($path, $name = null): self
    {
        $this->attachments[] = [
            'path' => $path,
            'name' => $name ?? basename($path)
        ];
        return $this;
    }

    /**
     * Establece datos del template
     */
    public function setTemplateData(array $data): self
    {
        $this->templateData = array_merge($this->templateData, $data);
        return $this;
    }

    /**
     * Establece metadata adicional
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);
        return $this;
    }
}
