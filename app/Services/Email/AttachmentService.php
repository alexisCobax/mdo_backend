<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Log;

/**
 * Servicio de alto nivel para manejo de adjuntos
 * Facilita la generación y gestión de adjuntos para emails
 */
class AttachmentService
{
    protected $attachmentManager;

    public function __construct(AttachmentManager $attachmentManager = null)
    {
        $this->attachmentManager = $attachmentManager ?? new AttachmentManager();
    }

    /**
     * Genera un PDF de cotización y retorna la ruta
     */
    public function generateCotizacionPdf(int $cotizacionId, array $cotizacionData): ?string
    {
        $filename = "cotizacion_{$cotizacionId}.pdf";

        $result = $this->attachmentManager->generateAndStorePdf(
            'pdf.cotizacion',
            ['cotizacion' => $cotizacionData],
            $filename
        );

        if ($result['success']) {
            return $result['path'];
        }

        Log::error('AttachmentService: Error al generar PDF de cotización', [
            'cotizacion_id' => $cotizacionId,
            'error' => $result['error'] ?? 'Error desconocido'
        ]);

        return null;
    }

    /**
     * Genera un PDF de invoice y retorna la ruta
     */
    public function generateInvoicePdf(int $invoiceId, array $invoiceData): ?string
    {
        $filename = "invoice_{$invoiceId}.pdf";

        $result = $this->attachmentManager->generateAndStorePdf(
            'pdf.invoice',
            ['invoice' => $invoiceData],
            $filename
        );

        if ($result['success']) {
            return $result['path'];
        }

        Log::error('AttachmentService: Error al generar PDF de invoice', [
            'invoice_id' => $invoiceId,
            'error' => $result['error'] ?? 'Error desconocido'
        ]);

        return null;
    }

    /**
     * Genera un PDF de proforma y retorna la ruta
     */
    public function generateProformaPdf(int $pedidoId, array $proformaData): ?string
    {
        $filename = "proforma_{$pedidoId}.pdf";

        $result = $this->attachmentManager->generateAndStorePdf(
            'pdf.proforma',
            ['proforma' => $proformaData],
            $filename
        );

        if ($result['success']) {
            return $result['path'];
        }

        Log::error('AttachmentService: Error al generar PDF de proforma', [
            'pedido_id' => $pedidoId,
            'error' => $result['error'] ?? 'Error desconocido'
        ]);

        return null;
    }

    /**
     * Obtiene la ruta del archivo fijo de transferencia bancaria
     */
    public function getTransferenciaBancariaPath(): ?string
    {
        $path = storage_path('app/public/fijos/Inf.TRANSFERENCIA_BANCARIA.pdf');

        if (file_exists($path)) {
            return $path;
        }

        Log::warning('AttachmentService: Archivo de transferencia bancaria no encontrado', [
            'path' => $path
        ]);

        return null;
    }

    /**
     * Prepara adjuntos para un EmailMessage
     * Valida y formatea los adjuntos correctamente
     */
    public function prepareAttachments(array $attachments): array
    {
        $validation = $this->attachmentManager->validateAttachments($attachments);

        if (!$validation['valid']) {
            Log::warning('AttachmentService: Algunos adjuntos son inválidos', [
                'errors' => $validation['errors']
            ]);
        }

        return $validation['attachments'];
    }

    /**
     * Prepara adjuntos estándar para cotización
     */
    public function prepareCotizacionAttachments(int $cotizacionId, bool $includeTransferencia = true): array
    {
        $attachments = [];

        // PDF de cotización
        $cotizacionPath = storage_path("app/public/tmpdf/cotizacion_{$cotizacionId}.pdf");
        if (file_exists($cotizacionPath)) {
            $attachments[] = [
                'path' => $cotizacionPath,
                'name' => "cotizacion_{$cotizacionId}.pdf"
            ];
        } else {
            Log::warning('AttachmentService: PDF de cotización no encontrado', [
                'path' => $cotizacionPath
            ]);
        }

        // Archivo de transferencia bancaria
        if ($includeTransferencia) {
            $transferenciaPath = $this->getTransferenciaBancariaPath();
            if ($transferenciaPath) {
                $attachments[] = [
                    'path' => $transferenciaPath,
                    'name' => 'Inf.TRANSFERENCIA_BANCARIA.pdf'
                ];
            }
        }

        return $this->prepareAttachments($attachments);
    }

    /**
     * Prepara adjuntos estándar para invoice
     */
    public function prepareInvoiceAttachments(int $invoiceId): array
    {
        $attachments = [];

        $invoicePath = storage_path("app/public/tmpdf/cotizacion_{$invoiceId}.pdf");
        if (file_exists($invoicePath)) {
            $attachments[] = [
                'path' => $invoicePath,
                'name' => "invoice_{$invoiceId}.pdf"
            ];
        } else {
            Log::warning('AttachmentService: PDF de invoice no encontrado', [
                'path' => $invoicePath
            ]);
        }

        return $this->prepareAttachments($attachments);
    }

    /**
     * Limpia archivos temporales antiguos
     */
    public function cleanOldFiles(int $maxAgeHours = 24): int
    {
        $maxAge = $maxAgeHours * 3600; // Convertir a segundos
        return $this->attachmentManager->cleanOldFiles('tmpdf', $maxAge);
    }
}
